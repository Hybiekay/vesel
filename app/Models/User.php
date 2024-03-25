<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
//use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
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
	'address',
	'phone_number',
	'profile_picture',
	'provider_id',
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
        'password' => 'hashed',
    ];

    /**
     * Get the products associated with the user.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function userAlerts()
    {
        return $this->hasMany(UserAlert::class);
    }    

public function businessDetails()
{
    return $this->hasOne(BusinessDetails::class);
}

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    /*
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
     */

    /*
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')
            ->orderBy('created_at', 'desc');
    }
     */    
}
