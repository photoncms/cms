<?php

namespace Photon\PhotonCms\Core\Entities\User;

use Photon\PhotonCms\Core\Entities\User\Contracts\UserInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, UserInterface
{
    use Authenticatable, Authorizable, CanResetPassword;
    use HasRoles;
    use Notifiable;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'password_created_at'
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Attributes which can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['email', 'password', 'first_name', 'last_name', 'confirmation_code', 'confirmed'];

    /**
     * Attributes which are excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
}
