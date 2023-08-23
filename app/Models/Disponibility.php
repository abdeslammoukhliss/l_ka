<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disponibility extends Model
{
    use HasFactory;

    protected $table = 'disponibilities';

    protected $fillable = [
        'day',
        'shift',
        'student_group'
    ];
}
