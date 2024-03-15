<?php

namespace Domain\Gyms\Models;

use Database\Factories\GymSubscriptionNoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use Support\Models\Entity;
use Support\Models\Traits\UserTracking;

class GymSubscriptionNote extends Entity implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use UserTracking;

    /**
     * @var string
     */
    protected $table = 'gym_subscription_notes';

    /**
     * @var string[]
     */
    protected $fillable = [
        'gym_subscription_id',
        'note',
    ];

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

    /**
     * @return GymSubscriptionNoteFactory
     */
    protected static function newFactory(): GymSubscriptionNoteFactory
    {
        return GymSubscriptionNoteFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function gymSubscription(): BelongsTo
    {
        return $this->belongsTo(GymSubscription::class);
    }
}
