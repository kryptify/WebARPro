<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;

use function Illuminate\Events\queueable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
     /*
    protected static function booted()
    {
        static::updated(queueable(function ($customer) {
        $customer->syncStripeCustomerDetails();
        }));
    }
    */


    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function subscriptions()
    {
        return $this
              ->hasMany(Subscription::class, $this->getForeignKey())
              ->orderBy('created_at', 'desc');
    }

    public function isAdmin()
    {
         if($this->role_id > 1) {
              return true;
          } else {
              return false;
          }

    }

}
