<?php

namespace Domain\Gyms\Models;

use Domain\Gyms\Enums\GymSubscriptionPaymentDetailType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use Support\Models\Entity;

class GymSubscriptionPaymentDetail extends Entity implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * @var string
     */
    protected $table = 'gym_subscription_payment_details';

    /**
     * @var string[]
     */
    protected $casts = [
        'type' => GymSubscriptionPaymentDetailType::class,
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'gym_subscription_payment_id',
        'type',
        'gym_subscription_member_id',
        'price',
        'quantity',
        'amount',
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
     * @return BelongsTo|null
     */
    public function gymSubscriptionPayment(): ?BelongsTo
    {
        return $this->belongsTo(GymSubscriptionPayment::class);
    }

    /**
     * @return BelongsTo|null
     */
    public function gymSubscriptionMember(): ?BelongsTo
    {
        return !empty($this->gym_subscription_member_id) ? $this->belongsTo(GymSubscriptionMember::class) : null;
    }

}
