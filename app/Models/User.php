<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'password', 'role', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Relasi ke tabel roles. Kolom string `role` tetap dipakai untuk membedakan admin utama. */
    public function accessRole()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->isAdmin() || in_array($permission, $this->accessRole?->permissions ?? [], true);
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->isAdmin() ? 'Admin' : ($this->accessRole?->name ?? 'Tanpa role');
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'reseller_id');
    }
}
