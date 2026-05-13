<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Status constants
     */
    const STATUS_TODO = 'todo';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_DONE = 'done';

    /**
     * Priority constants
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    /**
     * Scope: filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: filter by priority
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope: overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->toDateString())
                     ->where('status', '!=', self::STATUS_DONE);
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               $this->status !== self::STATUS_DONE;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_TODO => 'gray',
            self::STATUS_IN_PROGRESS => 'blue',
            self::STATUS_DONE => 'green',
            default => 'gray',
        };
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'green',
            self::PRIORITY_MEDIUM => 'yellow',
            self::PRIORITY_HIGH => 'red',
            default => 'gray',
        };
    }
}
