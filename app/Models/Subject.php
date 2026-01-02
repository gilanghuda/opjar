<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = ['code','name','description','credits'];

    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }

    public function files()
    {
        return $this->hasMany(LearningFile::class);
    }
}
