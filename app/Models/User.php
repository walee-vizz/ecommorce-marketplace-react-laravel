<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;
    use HasPanelShield;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
    public function isVendor(): bool
    {
        return $this->hasRole('vendor');
    }
    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function isSuperAdminOrAdmin(): bool
    {
        return $this->hasAnyRole(['super-admin', 'admin']);
    }
    public function isSuperAdminOrAdminOrVendor(): bool
    {
        return $this->hasAnyRole(['super-admin', 'admin', 'vendor']);
    }
}
