<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Validation rules for add user
     *
     * @return array
     */
    public static function validationRulesForAddUser()
    {
        return [
            'name'      => 'required|max:'.config('app.length.name').'|regex:'.config('app.validation_patterns.name_server'),
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:'.config('app.length.password_min').'|max:'.config('app.length.password_max').'|confirmed'
        ];
    }

    /**
     * Validation messages
     *
     * @var array
     */
    public static $validationMessages = [
        'name.required'                     => 'Please enter name',
        'name.max'                          => 'Name can be max 50 characters long',
        'name.regex'                        => 'Name is invalid',
        'mobile.required'                   => 'Please enter mobile number',
        'mobile.min'                        => 'Mobile number can be minimum 7 digits long',
        'mobile.max'                        => 'Mobile number can be max 11 digits long',
        'mobile.regex'                      => 'Mobile number must be 7-11 digits',
        'email.required'                    => 'Please enter email address',
        'email.email'                       => 'Email address is invalid',
        'email.unique'                      => 'This email address already exist',
        'old_password.required'             => 'Please enter old password',
        'old_password.old_password'         => 'Your old password is incorrect',
        'password.required'                 => 'Please enter password',
        'password.min'                      => 'Password can be minimum 6 characters long',
        'password.max'                      => 'Password can be max 20 characters long',
        'password.confirmed'                => 'Password and confirm password do not match',
        'password_confirmation.required'    => 'Please enter confirm password',
    ];
}
