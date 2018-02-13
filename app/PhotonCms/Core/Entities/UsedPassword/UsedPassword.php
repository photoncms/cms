<?php

namespace Photon\PhotonCms\Core\Entities\UsedPassword;

use Illuminate\Database\Eloquent\Model;

class UsedPassword extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'used_passwords';

    /**
     * Attributes which can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'password', 'created_at'];

    /**
     * Laravel timestamps disabled. Factory should worry about these.
     *
     * @var boolean
     */
    public $timestamps = false;
}