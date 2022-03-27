<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employees extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = true;
    public function Hobby()
    {
        return $this->hasOne(Hobbies::class, 'id', 'hobby_id');
    }
}