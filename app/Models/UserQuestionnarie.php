<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuestionnarie extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'user_questionnarie';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'group_id',
        'user_id',
        'questionnarie_id',
        'user_target_id',
        'rol',
        'weight',
        'real_weight',
        'start',
        'deadline',
    ];
}
