@extends('adminlte::page')

@section('title', 'Edit Studio')

@section('content_header')
    <h1 class="fw-bold text-primary">
        <i class="fas fa-camera-retro me-2"></i> Edit Studio
    </h1>
@stop

@section('content')
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4">

            <form action="{{ route('studio.update', $studio) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Studio</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $studio->name) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Telepon</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $studio->phone) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kota</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $studio->city) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Studio</label>
                    <textarea name="address" class="form-control" rows="3" required>{{ old('address', $studio->address) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Google Maps</label>
                    <input type="url" name="google_maps" class="form-control"
                        value="{{ old('google_maps', $studio->google_maps) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Studio</label>

                    @if ($studio->image)
                        <div class="mb-2">
                            <img src="{{ asset('uploads/studios/' . $studio->image) }}" style="height:80px"
                                class="rounded shadow-sm">
                        </div>
                    @endif

                    <input type="file" name="image" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ $studio->status ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$studio->status ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/studio" class="btn btn-light">Batal</a>
                    <button class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>

            </form>

        </div>
    </div>
@stop
