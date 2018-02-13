<?php

namespace Photon\PhotonCms\Core\Entities\EmailChangeRequest;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EmailChangeRequest extends Model
{
    use Notifiable;

    protected $fillable = [
        'user_id',
        'email',
        'confirmation_code',
        'used',
    ];
}