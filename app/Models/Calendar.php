<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    //esta linea hace que los campos sean fillables, se usa para el curso, pero no para entornos de producción.
    protected $guarded = [];
}
