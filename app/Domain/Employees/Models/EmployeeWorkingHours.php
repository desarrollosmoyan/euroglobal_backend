<?php

namespace Domain\Employees\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use Support\Models\Entity;

class EmployeeWorkingHours extends Entity implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * @var string
     */
    protected $table = 'employee_working_hours';

    protected $casts = [
        'work_schedule' => 'array'
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'employee_id',
        'date',
        'work_schedule'
    ];

    /**
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(static function ($record) {
            $record->created_by = Auth::id();
            $record->last_modified_by = Auth::id();
        });

        static::updating(static function ($record) {
            $record->last_modified_by = Auth::id();
        });
    }
}
