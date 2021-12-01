<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tasks';
    protected $fillable = ['description', 'type_id', 'title', 'priority', 'blocked'];

    public function users(): BelongsToMany
    {
      return $this->belongsToMany(User::class);
    }

    public function taskType(): HasOne
    {
      return $this->hasOne(TaskType::class);
    }

    public function taskDates(): HasMany
    {
        return $this->hasMany(TaskDate::class)->orderBy('activity_date');
    }

    protected static function booted()
    {
        static::deleted(function ($task) {
            // When the task is deleted, delete its dates
            $task->taskDates()->delete();
        });
    }

}
