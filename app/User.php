<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','login','active','image','sex'
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    /**
     * Роли, принадлежащие пользователю.
     */
    public function roles()
    {
        return $this->belongsToMany('Modules\Admin\Entities\Role');
    }

    public static function hasRole($code){
        // получить id текущего залогиненного юзера
        $user_id = Auth::id();
        if(!empty($user_id)){
            $roles = User::find($user_id)->roles;
            foreach ($roles as $role){
                if($role->code==$code || $role->code=='admin')
                    return TRUE;
            }
        }
        return FALSE;
    }

    public static function isAuthor($id){
        // получить id текущего залогиненного юзера
        $user_id = Auth::id();
        if(!empty($user_id)){
            if($user_id == $id)
                return TRUE;
        }
        return FALSE;
    }
}
