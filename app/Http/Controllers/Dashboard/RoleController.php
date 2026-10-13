<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();

        return view('dashboard.roles', compact('roles'));
    }

    public function store(Request $request)
    {
        $role = Role::create($this->validated($request));

        return back()->with('success', "Role {$role->name} dibuat. Sekarang bisa dipilih saat membuat akun.");
    }

    public function update(Request $request, Role $role)
    {
        $role->update($this->validated($request, $role));

        return back()->with('success', "Hak akses role {$role->name} disimpan. Berlaku langsung untuk semua penggunanya.");
    }

    public function destroy(Role $role)
    {
        if ($n = $role->users()->count()) {
            return back()->withErrors(['role' => "Role {$role->name} masih dipakai {$n} akun. Pindahkan akun tersebut ke role lain dulu."]);
        }

        $role->delete();

        return back()->with('success', "Role {$role->name} dihapus.");
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:50', Rule::unique('roles', 'name')->ignore($role)],
            'permissions'   => 'nullable|array',
            'permissions.*' => Rule::in(array_keys(Role::PERMISSIONS)),
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique'   => 'Nama role sudah dipakai.',
        ]);

        return ['name' => trim($data['name']), 'permissions' => array_values($data['permissions'] ?? [])];
    }
}
