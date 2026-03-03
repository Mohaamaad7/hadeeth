<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    // ===== Role Helpers =====

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isReviewer(): bool
    {
        return $this->role === 'reviewer';
    }

    public function isDataEntry(): bool
    {
        return $this->role === 'data_entry';
    }

    /**
     * هل يملك صلاحية معينة؟
     */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * اسم الدور بالعربي.
     */
    public function getRoleNameAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'مدير النظام',
            'data_entry' => 'مدخل بيانات',
            'reviewer' => 'مراجع',
            default => $this->role,
        };
    }

    /**
     * لون الـ badge حسب الدور.
     */
    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'danger',
            'data_entry' => 'info',
            'reviewer' => 'warning',
            default => 'secondary',
        };
    }

    // ===== Relationships =====

    /**
     * الأحاديث التي أدخلها هذا المستخدم.
     */
    public function enteredHadiths(): HasMany
    {
        return $this->hasMany(Hadith::class, 'entered_by');
    }

    /**
     * الأحاديث التي راجعها هذا المستخدم.
     */
    public function reviewedHadiths(): HasMany
    {
        return $this->hasMany(Hadith::class, 'reviewed_by');
    }
}
