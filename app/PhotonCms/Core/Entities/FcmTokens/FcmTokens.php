<?php

namespace Photon\PhotonCms\Core\Entities\FcmTokens;

use Illuminate\Database\Eloquent\Model;

class FcmTokens extends Model
{
    protected $table = 'fcm_tokens';
    protected $fillable = [
        'user',
        'token'
    ];
}