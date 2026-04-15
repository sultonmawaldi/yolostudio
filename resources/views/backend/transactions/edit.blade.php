@extends('adminlte::page')

@section('title', 'Edit Transaksi')

@section('content_header')

    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            {{-- Judul --}}
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-edit text-primary mr-2"></i>
                    Edit Transaksi
                </h1>
            </div>

            {{-- Breadcrumb --}}
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Beranda
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('transactions.index') }}">Transaksi</a>
                    </li>
                    <li class="breadcrumb-item active">Edit Transaksi</li>
                </ol>
            </div>

        </div>
    </div>

@stop


@section('content')

    <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- ===================== --}}
            {{-- KONTEN KIRI --}}
            {{-- ===================== --}}
            <div class="col-md-8">

                {{-- INFORMASI TRANSAKSI --}}
                <div class="card card-light">

                    <div class="card-header">
                        <h3 class="card-title">Informasi Transaksi</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label>Kode Transaksi</label>
                            <input type="text" class="form-control" value="{{ $transaction->transaction_code }}"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label>Pengguna</label>
                            <input type="text" class="form-control"
                                value="{{ optional($transaction->appointment)->name ?? '-' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Layanan</label>
                            <input type="text" class="form-control"
                                value="{{ optional(optional($transaction->appointment)->service)->title ?? '-' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Total Biaya</label>
                            <input type="text" class="form-control"
                                value="Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Total Dibayar (DP / Pembayaran Masuk)</label>
                            <input type="text" class="form-control"
                                value="Rp {{ number_format($transaction->amount, 0, ',', '.') }}" readonly>
                        </div>

                    </div>

                </div>


                {{-- METODE PEMBAYARAN --}}
                <div class="card card-light">

                    <div class="card-header">
                        <h3 class="card-title">Metode Pembayaran</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <label>Metode Pembayaran DP</label>
                            <input type="text" class="form-control" value="{{ $transaction->dp_method ?? '-' }}"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label>Metode Pelunasan</label>
                            <input type="text" class="form-control" value="{{ $transaction->pelunasan_method ?? '-' }}"
                                readonly>
                        </div>

                    </div>

                </div>

            </div>


            {{-- ===================== --}}
            {{-- SIDEBAR --}}
            {{-- ===================== --}}
            <div class="col-md-4">

                <div class="sticky-top">

                    <div class="card card-primary">

                        <div class="card-header">
                            <h3 class="card-title">Status Pembayaran</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>


                        <div class="card-body pb-0">

                            <div class="form-group">

                                <label>Status</label>

                                <select name="payment_status" class="form-control" required>

                                    <option value="Pending"
                                        {{ $transaction->payment_status == 'Pending' ? 'selected' : '' }}>
                                        Menunggu
                                    </option>

                                    <option value="DP" {{ $transaction->payment_status == 'DP' ? 'selected' : '' }}>
                                        Uang Muka (DP)
                                    </option>

                                    <option value="Paid" {{ $transaction->payment_status == 'Paid' ? 'selected' : '' }}>
                                        Lunas
                                    </option>

                                    <option value="Failed"
                                        {{ $transaction->payment_status == 'Failed' ? 'selected' : '' }}>
                                        Gagal
                                    </option>

                                </select>

                                <small class="text-muted">
                                    Ubah status pembayaran transaksi
                                </small>

                            </div>


                            <div class="form-group mt-4 d-flex justify-content-end">

                                <a href="{{ route('transactions.index') }}" class="btn btn-secondary mr-2">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Perbarui
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </form>

@stop
