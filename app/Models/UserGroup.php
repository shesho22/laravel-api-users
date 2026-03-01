<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    // Nombre exacto de la tabla en la DB
    protected $table = 'user_group';

    // Desactivamos timestamps si tu tabla no tiene 'created_at' y 'updated_at'
    // Si los tiene, puedes borrar esta línea.
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'name',
        'bind',
        'creation'
    ];
}
