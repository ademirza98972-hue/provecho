@extends('layouts.app')
@section('title', 'Role')
@section('content')

<div class="stack">

    <div class="page-head">
        <div>
            <h2 class="page-title">Role &amp; hak akses</h2>
            <p class="page-sub">Buat role dan centang fitur yang boleh diakses. Setelah itu pilih role saat membuat akun di halaman Pengguna.</p>
        </div>
    </div>

    @php $perms = \App\Models\Role::PERMISSIONS; @endphp
    <div class="panel" id="roles">
        <div class="panel-head">
            <div>
                <span class="panel-title">Daftar role</span>
                <p class="panel-sub">Fitur dasar (lihat, aktifkan, dan ubah data card miliknya) selalu bisa dipakai semua role. Centang fitur tambahan di bawah. Perubahan langsung berlaku untuk semua akun dengan role tersebut.</p>
            </div>
        </div>

        <div class="role-list">
            @foreach($roles as $role)
            <div class="role-row" x-data="{ edit: false }">
                <div class="role-sum" x-show="!edit">
                    <div class="role-info">
                        <span class="role-name">{{ $role->name }}</span>
                        <span class="hint">{{ $role->users_count }} akun</span>
                        <div class="chips">
                            @forelse($role->permissions as $p)
                                @isset($perms[$p])<span class="chip">{{ $perms[$p][0] }}</span>@endisset
                            @empty
                                <span class="hint">Hanya fitur dasar</span>
                            @endforelse
                        </div>
                    </div>
                    <button type="button" class="btn btn-ghost btn-sm" @click="edit = true">Ubah</button>
                </div>

                <form x-show="edit" x-cloak method="POST" action="{{ route('dashboard.roles.update', $role) }}" class="form-stack">
                    @csrf @method('PUT')
                    @include('dashboard._role-fields', ['role' => $role, 'prefix' => 'role-' . $role->id])
                    <div class="role-actions">
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                        <button type="button" class="btn btn-ghost btn-sm" @click="edit = false">Batal</button>
                        @if($role->users_count === 0)
                        <button type="submit" form="del-role-{{ $role->id }}" class="btn btn-ghost btn-sm del">Hapus role</button>
                        @else
                        <span class="hint">Role yang masih dipakai akun tidak bisa dihapus.</span>
                        @endif
                    </div>
                </form>
                @if($role->users_count === 0)
                <form id="del-role-{{ $role->id }}" method="POST" action="{{ route('dashboard.roles.destroy', $role) }}" hidden
                      onsubmit="return confirm('Hapus role {{ $role->name }}?')">
                    @csrf @method('DELETE')
                </form>
                @endif
            </div>
            @endforeach

            <div class="role-row" x-data="{ open: @js($roles->isEmpty() || $errors->has('name')) }">
                <button type="button" class="btn btn-outline btn-sm" x-show="!open" @click="open = true">+ Buat role baru</button>
                <form x-show="open" x-cloak method="POST" action="{{ route('dashboard.roles.store') }}" class="form-stack">
                    @csrf
                    <span class="role-name">Role baru</span>
                    @include('dashboard._role-fields', ['role' => null, 'prefix' => 'role-new'])
                    <div class="role-actions">
                        <button type="submit" class="btn btn-primary btn-sm">Buat role</button>
                        <button type="button" class="btn btn-ghost btn-sm" @click="open = false">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
[x-cloak] { display: none !important; }
.page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
.page-title { font-size: 20px; font-weight: 700; letter-spacing: -.02em; }
.page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; max-width: 70ch; }
.panel-sub { font-size: 12.5px; color: var(--muted); margin-top: 3px; }
.role-list > .role-row { padding: 14px 18px; border-bottom: 1px solid #F3F4F6; }
.role-list > .role-row:last-child { border-bottom: 0; }
.role-sum { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
.role-info { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 10px; min-width: 0; }
.role-name { font-weight: 700; }
.chips { flex-basis: 100%; display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px; }
.chip { font-size: 12px; font-weight: 600; padding: 2px 9px; border-radius: 99px; background: var(--accent-soft); color: var(--accent-dark); }
.perm-label { font-size: 12.5px; font-weight: 600; }
.perm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 8px; }
.perm { display: flex; gap: 10px; align-items: flex-start; padding: 10px 12px; border: 1px solid var(--border); border-radius: 9px; cursor: pointer; font-weight: 400; }
.perm:has(input:checked) { border-color: var(--accent); background: var(--accent-soft); }
.perm input { margin-top: 2px; }
.perm b { display: block; font-size: 13px; }
.perm .hint { display: block; margin-top: 1px; }
.role-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
.role-actions .del:hover { color: var(--bad); background: var(--bad-soft); }
</style>
@endpush

@endsection
