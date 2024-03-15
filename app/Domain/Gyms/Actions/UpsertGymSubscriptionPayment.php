<?php

namespace Domain\Gyms\Actions;

use Domain\Gyms\Contracts\Repositories\GymSubscriptionPaymentsRepository;
use Domain\Gyms\Enums\GymSubscriptionPaymentDetailType;
use Domain\Gyms\Models\GymSubscriptionPayment;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UpsertGymSubscriptionPayment
{
    private GymSubscriptionPaymentsRepository $repository;
    private Factory $validatorFactory;

    /**
     * @param GymSubscriptionPaymentsRepository $repository
     * @param Factory $validatorFactory
     */
    public function __construct(
        GymSubscriptionPaymentsRepository $repository,
        Factory $validatorFactory
    ) {
        $this->repository = $repository;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @param array $data
     * @return GymSubscriptionPayment
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function __invoke(array $data): GymSubscriptionPayment
    {
        $validator = $this->validatorFactory->make($data, $this->rules($data));

        $validator->validate();

        $method = array_key_exists('id', $data) ? 'edit' : 'add';

        $repository = $this->repository;

        DB::transaction(static function () use ($repository, $method, $data, &$entity) {
            $entity = $repository->$method($data);
            foreach ($data['details'] as $detail) {
                app(UpsertGymSubscriptionPaymentDetail::class)([
                    'gym_subscription_payment_id' => $entity->id,
                    ...$detail
                ]);
            }
        });

        return $entity->refresh();
    }

    /**
     * @param $data
     * @return array
     */
    private function rules($data): array
    {
        $rules = [
            'gym_subscription_id' => 'required|numeric',
            'order_id' => 'required|numeric',
            'previous_expiration_date' => 'nullable|date|date_format:Y-m-d',
            'next_expiration_date' => 'required|date|date_format:Y-m-d',
            'amount' => 'required|numeric',
            'date' => 'required|date|date_format:Y-m-d',
            'details' => 'required|array',
            'details.*.id' => 'required|exists:gym_subscription_payment_details',
            'details.*.type' => 'required|in:' . implode(
                    ',',
                    collect(GymSubscriptionPaymentDetailType::cases())->pluck('value')->toArray()
                ),
            'details.*.gym_subscription_member_id' => 'required|numeric',
            'details.*.price' => 'required|numeric',
            'details.*.quantity' => 'required|numeric',
            'details.*.amount' => 'required|numeric',
        ];

        if (array_key_exists('id', $data)) {
            $rules['id'] = 'required|exists:gym_subscription_payments';
        }

        return $rules;
    }
}
