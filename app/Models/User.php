<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- HELPER METHODS (PENTING AGAR ROUTE BERJALAN) ---
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isKasir()
    {
        return $this->role === 'kasir';
    }

    public function isGudang()
    {
        return $this->role === 'gudang';
    }
}
