<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    use HasFactory;
    
    protected $table = 'students_progresses';

    protected $fillable = [
        'student',
        'group_project',
    ];
}
