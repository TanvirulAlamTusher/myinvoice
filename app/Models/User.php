<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasFactory, Notifiable,   HasRoles;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
       
    ];

    /**
     * Hidden fields (never show in JSON/API)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Helper: check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Helper: check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }
}
