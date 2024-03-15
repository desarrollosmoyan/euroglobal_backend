<?php

namespace Domain\Gyms\Models;

use Support\Models\Entity;
use Domain\Clients\Models\Client;
use Domain\Gyms\Models\GymFeeType;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;
use Domain\Gyms\Enums\GymSubscriptionPaymentType;
use Domain\Gyms\Models\GymSubscription;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GymSubscriptionVersion extends Entity implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'gym_subscription_versions';

    protected $casts = [
        'client_id' => 'integer',
        'gym_fee_type_id' => 'integer',
        'version' => 'integer',
        'duration_number_of_days' => 'integer',
        'activation_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'expiration_date' => 'date',
        'payment_day' => 'integer',
        'biweekly_payment_day' => 'integer',
        'payment_type' => GymSubscriptionPaymentType::class,
        'gym_subscription_id' => 'integer',
    ];

    protected $fillable = [
        'client_id',
        'gym_fee_type_id',
        'gym_fee_type_name',
        'version',
        'duration_number_of_days',
        'price',
        'price_beneficiaries',
        'activation_date',
        'start_date',
        'end_date',
        'expiration_date',
        'payment_day',
        'biweekly_payment_day',
        'payment_type',
        'gym_subscription_id'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function gymFeeType(): BelongsTo
    {
        return $this->belongsTo(GymFeeType::class, 'gym_fee_type_id');
    }

    public function gymSubscription(): BelongsTo
    {
        return $this->belongsTo(GymSubscription::class, 'gym_subscription_id');
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
