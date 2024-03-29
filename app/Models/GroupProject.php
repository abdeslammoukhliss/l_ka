<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupProject extends Model
{
    use HasFactory;
    
    protected $table = 'groups_projects';

    protected $fillable = [
        'project',
        'group',
        'deadline',
        'affected_date'
    ];
}
