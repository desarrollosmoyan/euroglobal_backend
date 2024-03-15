<?php

namespace Domain\Gyms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use Support\Models\Entity;

class GymSubscriptionPayment extends Entity implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * @var string
     */
    protected $table = 'gym_subscription_payments';

    /**
     * @var string[]
     */
    protected $casts = [
        'previous_expiration_date' => 'date',
        'next_expiration_date' => 'date',
        'date' => 'date',
        'order_id' => 'integer',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'gym_subscription_id',
        'order_id',
        'previous_expiration_date',
        'next_expiration_date',
        'amount',
        'date',
        'created_by',
        'last_modified_by'
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
     * @return BelongsTo
     */
    public function gymSubscription(): BelongsTo
    {
        return $this->belongsTo(GymSubscription::class);
    }

    /**
     * @return HasMany
     */
    public function gymSubscriptionPaymentDetails(): HasMany
    {
        return $this->hasMany(GymSubscriptionPaymentDetail::class);
    }
}
