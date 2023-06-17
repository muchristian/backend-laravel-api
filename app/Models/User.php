<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PreferenceCategories;
use App\Models\PreferenceAuthors;
use App\Models\PreferenceSources;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['id' => $this->id];
    }

    public function preferenceCategory(): HasMany
    {
        return $this->hasMany(PreferenceCategories::class)->where('is_deleted', 0)->orderBy('category', 'asc');
    }

    public function preferenceSource(): HasMany
    {
        return $this->hasMany(PreferenceSources::class)->where('is_deleted', 0)->orderBy('source', 'asc');
    }
    
    public function PreferenceAuthor(): HasMany
    {
        return $this->hasMany(PreferenceAuthors::class)->where('is_deleted', 0)->orderBy('author', 'asc');
    }
}
