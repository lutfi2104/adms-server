<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'entry_time',
        'exit_time',
        'duration_seconds',
        'status',
        'entry_sn',
        'exit_sn',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    /**
     * Get the employee associated with this access session.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
