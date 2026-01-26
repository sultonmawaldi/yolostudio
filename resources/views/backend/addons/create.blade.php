@extends('adminlte::page')

@section('title', 'Tambah Addon')

@section('content_header')
    <h1>Tambah Addon</h1>
@stop

@section('content')
    <div class="container-fluid">

        <div class="card">
            <div class="card-body">
                <form action="{{ route('addons.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="code">Code</label>
                        <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Addon</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="price">Harga</label>
                        <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="unit">Unit</label>
                        <select name="unit" id="unit" class="form-control" required>
                            <option value="person" {{ old('unit') == 'person' ? 'selected' : '' }}>Person</option>
                            <option value="minute" {{ old('unit') == 'minute' ? 'selected' : '' }}>Minute</option>
                            <option value="item" {{ old('unit') == 'item' ? 'selected' : '' }}>Item</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="max_qty">Max Qty</label>
                        <input type="number" name="max_qty" id="max_qty" class="form-control"
                            value="{{ old('max_qty') }}">
                    </div>

                    <div class="form-group">
                        <label for="is_active">Aktif</label>
                        <select name="is_active" id="is_active" class="form-control">
                            <option value="1" selected>Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">Simpan</button>
                    <a href="{{ route('addons.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>

    </div>
@stop
