<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Str;
use App\Models\Coupon;

class PaymentController extends Controller
{
    public function getSnapToken(Request $request)
    {

        $request->validate([
            'name'  => 'required|string|min:3',
            'email' => 'required|email',
            'phone' => 'required|string|min:8',
        ]);
        /**
         * ===============================
         * MIDTRANS CONFIG
         * ===============================
         */
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        /**
         * ===============================
         * ORDER ID
         * ===============================
         */
        $orderId = 'ORDER-' . date('Ymd') . '-' . strtoupper(Str::random(8));

        /**
         * ===============================
         * REQUEST DATA
         * ===============================
         */
        $paymentType   = $request->payment_type ?? 'full';
        $serviceTitle  = $request->service_title ?? 'Layanan';
        $servicePrice  = (int) ($request->service_price ?? 0);
        $totalAmount   = (int) ($request->total_amount ?? 0);
        $dpAmount      = (int) ($request->dp_amount ?? 0);
        $discountAmount = (int) ($request->discount_amount ?? 0);

        /**
         * ===============================
         * KUPON
         * ===============================
         */
        $couponCode = 'KUPON';

        if ($request->coupon_id) {
            $coupon = Coupon::find($request->coupon_id);
            if ($coupon) {
                $couponCode = $coupon->code;
            }
        }

        $items = [];
        $sisaPayment = 0;

        /**
         * ===============================
         * MODE DP
         * ===============================
         */
        if ($paymentType === 'dp') {

            $items[] = [
                'id'       => 'DP-' . strtoupper(uniqid()),
                'price'    => max(1000, $dpAmount),
                'quantity' => 1,
                'name'     => "DP - {$serviceTitle}",
            ];

            if ($discountAmount > 0) {
                $items[] = [
                    'id'       => 'DISC-' . strtoupper($couponCode) . '-' . strtoupper(uniqid()),
                    'price'    => -abs($discountAmount),
                    'quantity' => 1,
                    'name'     => "Potongan Kupon ({$couponCode})",
                ];
            }

            $totalAfterDiscount = max(0, $totalAmount - $discountAmount);
            $sisaPayment = max(0, $totalAfterDiscount - $dpAmount);

            $grossAmount = max(1000, $dpAmount);
        }

        /**
         * ===============================
         * MODE FULL PAYMENT
         * ===============================
         */
        else {

            if ($servicePrice <= 0) {
                return response()->json([
                    'error' => 'Service price invalid'
                ], 422);
            }

            // Layanan utama
            $items[] = [
                'id'       => 'SERVICE-' . strtoupper(uniqid()),
                'price'    => $servicePrice,
                'quantity' => 1,
                'name'     => $serviceTitle,
            ];

            // Addons
            $addons = $request->addons ?? [];

            if (is_array($addons)) {
                foreach ($addons as $addon) {
                    if (
                        isset($addon['name'], $addon['price'], $addon['qty']) &&
                        (int) $addon['price'] > 0 &&
                        (int) $addon['qty'] > 0
                    ) {
                        $items[] = [
                            'id'       => 'ADDON-' . strtoupper(uniqid()),
                            'price'    => (int) $addon['price'],
                            'quantity' => (int) $addon['qty'],
                            'name'     => $addon['name'],
                        ];
                    }
                }
            }

            // Diskon
            if ($discountAmount > 0) {
                $items[] = [
                    'id'       => 'DISC-' . strtoupper($couponCode) . '-' . strtoupper(uniqid()),
                    'price'    => -abs($discountAmount),
                    'quantity' => 1,
                    'name'     => "Potongan Kupon ({$couponCode})",
                ];
            }

            $grossAmount = max(1000, array_sum(array_map(
                fn($item) => ((int) $item['price']) * ((int) $item['quantity']),
                $items
            )));
        }

        /**
         * ===============================
         * MIDTRANS PAYLOAD
         * ===============================
         */
        $payload = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($payload);

            return response()->json([
                'token'                  => $snapToken,
                'order_id'               => $orderId,
                'gross_amount'           => $grossAmount,
                'discount_amount'        => $discountAmount,
                'sisa_payment'           => $sisaPayment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to generate token',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
