<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\VerifyEmailQueued;
use Spatie\Permission\Traits\HasRoles;
use App\Components\QueueNames;

class Users extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Sets the name and name_lowercase properties.
     *
     * @param string $name
     * @return void
     */
    public function setNameAttribute(string $name): void {
        $this->attributes['name'] = $name;
        $this->attributes['name_lowercase'] = strtolower($name);
    }

    /**
     * Sets the email and email_lowercase properties.
     *
     * @param string $email
     * @return void
     */
    public function setEmailAttribute(string $email): void {
        $this->attributes['email'] = $email;
        $this->attributes['email_lowercase'] = strtolower($email);
    }

    /**
     * Sends the verification email for this user.
     *
     * @return void
     */
    public function sendEmailVerificationNotification() {
        $this->notify((new VerifyEmailQueued())->onQueue(QueueNames::EMAILS));
    }
}
