<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function getSnapToken(Request $request)
    {
        // ⚙️ Midtrans Config
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // 🆔 Order ID profesional
        $orderId = 'ORDER-' . date('Ymd') . '-' . strtoupper(Str::random(8));

        // 🔢 Ambil data dari request
        $additionalPeople = (int) ($request->additional_people ?? 0);
        $extraPricePerPerson = (int) ($request->extra_price_per_person ?? 0);
        $dpAmount = (int) ($request->dp_amount ?? 0);
        $totalAmount = (int) ($request->total_amount ?? 0);
        $serviceTitle = $request->service_title ?? 'Layanan';
        $paymentType = $request->payment_type ?? 'full';
        $couponId = $request->coupon_id ?? null;
        $couponCode = 'KUPON';

        if ($couponId) {
            // Ambil kode kupon asli dari database
            $coupon = \App\Models\Coupon::find($couponId);
            if ($coupon) {
                $couponCode = $coupon->code; // kode kupon asli
                $discountAmount = $coupon->discount_amount; // optional, jika ingin override
            }
        }
        $discountAmount = (int) ($request->discount_amount ?? $request->input('discount-amount', 0));

        // 🧮 Perhitungan dasar
        $additionalTotal = $additionalPeople * $extraPricePerPerson;
        $totalAfterDiscount = max(0, $totalAmount - $discountAmount);
        $items = [];

                // 💳 === MODE DP ===
        if ($paymentType === 'dp') {
            // Item utama (DP yang benar-benar dibayar)
            $items[] = [
                'id' => 'ITEM-' . strtoupper(uniqid()),
                'price' => $dpAmount, // hanya DP dibayar
                'quantity' => 1,
                'name' => "DP - {$serviceTitle}"
            ];

            // Tampilkan potongan kupon dulu (untuk UI)
            if ($discountAmount > 0) {
                $items[] = [
                    'id' => 'INFO-DISC-' . strtoupper($couponCode ?? 'COUPON') . '-' . strtoupper(uniqid()),
                    'price' => 0, // hanya informasi
                    'quantity' => 1,
                    'name' => "Potongan Kupon ({$couponCode}): - Rp" . number_format($discountAmount, 0, ',', '.')
                ];
            }

            // Hitung & tampilkan sisa pembayaran
            $sisaPayment = max(0, $totalAfterDiscount - $dpAmount);
            $items[] = [
                'id' => 'INFO-SISA-' . strtoupper(uniqid()),
                'price' => 0, // tidak dihitung Midtrans
                'quantity' => 1,
                'name' => "Sisa Pembayaran: Rp " . number_format($sisaPayment, 0, ',', '.')
            ];

            // Hanya DP yang dibayar ke Midtrans
            $grossAmount = max(1000, $dpAmount);
        }

        // 💳 === MODE FULL PAYMENT ===
        else {
            $servicePrice = max(0, $totalAmount - $additionalTotal);

            $items[] = [
                'id' => 'ITEM-' . strtoupper(uniqid()),
                'price' => $servicePrice,
                'quantity' => 1,
                'name' => $serviceTitle
            ];

            if ($additionalPeople > 0) {
                $items[] = [
                    'id' => 'ITEM-' . strtoupper(uniqid()),
                    'price' => $extraPricePerPerson,
                    'quantity' => $additionalPeople,
                    'name' => "Tambahan Orang"
                ];
            }

            if ($discountAmount > 0) {
                $items[] = [
                    'id' => 'DISC-' . strtoupper($couponCode) . '-' . strtoupper(uniqid()),
                    'price' => -$discountAmount,
                    'quantity' => 1,
                    'name' => "Potongan Kupon ({$couponCode})"
                ];
            }

            $sisaPayment = 0;
            $grossAmount = max(1000, $totalAfterDiscount);
        }

        // 🔖 Custom field tambahan
        // 🔖 Custom field tambahan (string rapi)
$customField = sprintf(
    "Sisa: Rp%s | Total Layanan: Rp%s | Tambahan Orang: %d (Rp%s) | Diskon: Rp%s | Kupon: %s",
    number_format($sisaPayment, 0, ',', '.'),
    number_format($totalAmount, 0, ',', '.'),
    $additionalPeople,
    number_format($additionalTotal, 0, ',', '.'),
    number_format($discountAmount, 0, ',', '.'),
    strtoupper($couponCode)
);


        // 🧾 Payload ke Midtrans
        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount, // hanya DP yang dibayar
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $request->name ?? 'Guest',
                'email' => $request->email ?? 'noemail@example.com',
                'phone' => $request->phone ?? '-',
            ],
            'custom_field1' => $customField
        ];

        try {
            $snapToken = Snap::getSnapToken($payload);

            return response()->json([
                'token' => $snapToken,
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'discount_amount' => $discountAmount,
                'sisa_payment' => $sisaPayment,
                'total_after_discount' => $totalAfterDiscount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate token',
                'message' => $e->getMessage(),
                'payload' => $payload
            ], 500);
        }
    }
}
