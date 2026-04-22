@extends('adminlte::page')

@section('title', 'Pelunasan Midtrans')

@section('content_header')
    <div class="page-title-wrapper text-center mb-4">
        <h1 class="page-title">
            <i class="fa fa-credit-card me-2"></i>
            Pelunasan Midtrans
        </h1>
        <div class="title-divider"></div>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body p-4">

                    {{-- Header --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-0">
                                {{ $transaction->transaction_code }}
                            </h5>
                            <small class="text-muted">
                                ID Pemesanan :
                                {{ $transaction->appointment->booking_id ?? '-' }}
                            </small>
                        </div>

                        {{-- Status Badge --}}
                        <span
                            class="badge px-3 py-2
                            @if ($transaction->payment_status == 'Paid') bg-success
                            @elseif($transaction->payment_status == 'DP')
                                bg-warning
                            @else
                                bg-secondary @endif
                        ">
                            {{ $transaction->payment_status }}
                        </span>
                    </div>

                    <hr>

                    {{-- Info Box --}}
                    <div class="row g-3">

                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <small class="text-muted d-block">Total Tagihan</small>
                                <strong class="fs-5">
                                    Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <small class="text-muted d-block">Sudah Dibayar</small>
                                <strong class="fs-5 text-success">
                                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3 text-center">
                                <small class="text-white d-block fw-semibold">
                                    Sisa Pembayaran
                                </small>
                                <strong class="fs-4 text-white">
                                    Rp
                                    {{ number_format(max($transaction->total_amount - $transaction->amount, 0), 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>

                    </div>

                    {{-- Button --}}
                    <div class="mt-4">

                        @if ($transaction->payment_status !== 'Paid')
                            <button id="pay-button" class="btn btn-primary w-100 py-2 rounded-3 shadow-sm">
                                <i class="fas fa-wallet me-2"></i>
                                Bayar Sisa Sekarang
                            </button>
                        @else
                            <div class="alert alert-success text-center rounded-3 mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                Transaksi sudah lunas
                            </div>
                        @endif

                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@section('css')
    <style>
        /* Paksa scrollbar selalu muncul supaya tidak layout shift */
        html {
            overflow-y: scroll;
        }

        /* ===== PAGE TITLE STYLE ===== */

        .page-title-wrapper {
            margin-top: 10px;
        }

        .page-title {
            font-weight: 700;
            font-size: 1.8rem;
            color: #2c3e50;
            letter-spacing: 0.4px;
        }

        .page-title i {
            color: #007bff;
        }

        .title-divider {
            width: 70px;
            height: 4px;
            margin: 12px auto 0;
            border-radius: 10px;
            background: linear-gradient(90deg, #007bff, #00c4ff);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-title {
                font-size: 1.4rem;
            }

            .title-divider {
                width: 50px;
                height: 3px;
            }
        }
    </style>
@stop

@section('js')

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Midtrans Snap --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const payButton = document.getElementById('pay-button');
            if (!payButton) return;

            payButton.addEventListener('click', function(e) {
                e.preventDefault();

                snap.pay('{{ $snapToken }}', {

                    onSuccess: function(result) {

                        Swal.fire({
                            title: 'Memverifikasi Pembayaran...',
                            text: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        window.location.href =
                            "{{ route('member.payment.finish', $transaction->id) }}" +
                            "?transaction_status=" + result.transaction_status +
                            "&order_id=" + result.order_id;
                    },

                    onPending: function() {
                        Swal.fire({
                            icon: 'info',
                            title: 'Menunggu Pembayaran',
                            text: 'Silakan selesaikan pembayaran di Midtrans.',
                            confirmButtonColor: '#0d6efd'
                        });
                    },

                    onError: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Pembayaran Gagal',
                            text: 'Terjadi kesalahan saat memproses pembayaran.',
                            confirmButtonColor: '#dc3545'
                        });
                    },

                    onClose: function() {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Popup Ditutup',
                            text: 'Anda belum menyelesaikan pembayaran.'
                        });
                    }

                });

            });

        });
    </script>

@endsection
