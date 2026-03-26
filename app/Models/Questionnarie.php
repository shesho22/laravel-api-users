<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnarie extends Model
{

    protected $table = 'questionnarie'; // si tu tabla es singular como en tu SQL

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;


    protected $fillable = [
        'id',
        'type',
        'name',
        'description',
        'start',
        'deadline',
    ];

    protected $casts = [
        'start' => 'date',
        'deadline' => 'date',
    ];
}
