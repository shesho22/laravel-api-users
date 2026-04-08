<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionnarieGroup extends Model
{
    protected $table = 'questionnarie_group';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'company_id',
        'name',
        'bind',
        'creation',
    ];
}
