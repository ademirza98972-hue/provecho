@extends('layouts.app')
@section('title', 'Pengaturan')
@section('content')

<div class="stack" style="max-width:480px">

    <div class="panel">
        <div class="panel-head">
            <span class="panel-title">Ubah Password</span>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('dashboard.settings.password') }}" class="form-stack">
                @csrf @method('PUT')

                <div class="field">
                    <label for="current_password">Password Lama</label>
                    <input type="password" id="current_password" name="current_password" required>
                    @error('current_password')<span class="hint" style="color:var(--bad)">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password" required minlength="6">
                    @error('password')<span class="hint" style="color:var(--bad)">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">Simpan Password</button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
