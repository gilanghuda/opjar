<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agenda extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title','description','date','start_time','end_time',
        'lecturer_id','subject_id','class_id','material','photo','attendance_taken','created_by'
    ];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function files()
    {
        return $this->hasMany(LearningFile::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
