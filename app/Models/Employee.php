<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'nik',
        'name',
        'gender',
        'department',
    ];

    /**
     * Get the access sessions for the employee.
     */
    public function accessSessions()
    {
        return $this->hasMany(AccessSession::class, 'employee_id', 'employee_id');
    }
}
