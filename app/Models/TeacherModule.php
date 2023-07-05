<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherModule extends Model
{
    use HasFactory;

    protected $table = 'teachers_modules';

    protected $fillable = [
        'teacher',
        'module',
    ];
}
