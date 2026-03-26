<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Company extends Model
{
        protected $table = 'company';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'description',
        'creation',
    ];
    protected $casts = [
    'creation' => 'datetime',
];
}
