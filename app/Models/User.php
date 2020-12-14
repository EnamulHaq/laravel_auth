<?php

namespace App\Models;

use App\Mail\PasswordResetVerificationCode;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\EmailVerificationCode;

use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'verification_code',
        'password',
        'remember_token',
    ];

    /**
     * Send Email verification code for registered user
     */
    public function sendEmailVerificationCode()
    {
        $this->generateVerificationCode();
        Mail::to($this)->send( new EmailVerificationCode($this) );
    }



    /**
     * Return generate verification code
     *
     * @return int|mixed
     */
    public function generateVerificationCode()
    {
        $this->verification_code = mt_rand(100000, 999999);
        $this->save();

        return $this->verification_code;
    }

    public function markEmailVerified()
    {
        $this->email_verified_at = Carbon::now();
        $this->verification_code = null;
        $this->save();
    }

    public function changePassword($password)
    {
        $this->password = Hash::make($password);
        $this->save();
    }
    public function passwordResetVerificationCode()
    {
        $this->generateVerificationCode();
        Mail::to($this)->send( new PasswordResetVerificationCode($this) );
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }
}
