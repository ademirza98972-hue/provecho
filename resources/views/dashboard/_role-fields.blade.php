{{-- Isian role, dipakai form buat dan ubah. $role null = role baru. --}}
@php $checked = $role ? $role->permissions : (old('permissions') ?? []); @endphp
<div class="field">
    <label for="{{ $prefix }}-name">Nama role</label>
    <input type="text" id="{{ $prefix }}-name" name="name" maxlength="50" required
           value="{{ $role ? $role->name : old('name') }}" placeholder="mis. Sales, Kasir Toko">
</div>
<div class="field">
    <span class="perm-label">Fitur tambahan yang boleh diakses</span>
    <div class="perm-grid">
        @foreach(\App\Models\Role::PERMISSIONS as $key => [$label, $desc])
        <label class="perm">
            <input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, $checked, true))>
            <span><b>{{ $label }}</b><span class="hint">{{ $desc }}</span></span>
        </label>
        @endforeach
    </div>
</div>
