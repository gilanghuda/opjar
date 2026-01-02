<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use SoftDeletes;

    protected $table = 'classes';
    protected $fillable = ['code','name','description','capacity'];

    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'class_id');
    }

    public function files()
    {
        return $this->hasMany(LearningFile::class, 'class_id');
    }
}
