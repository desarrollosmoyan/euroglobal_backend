<?php

namespace Domain\Gyms\Actions;

use Domain\Gyms\Contracts\Repositories\GymSubscriptionPaymentDetailsRepository;
use Domain\Gyms\Enums\GymSubscriptionPaymentDetailType;
use Domain\Gyms\Models\GymSubscriptionPaymentDetail;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

class UpsertGymSubscriptionPaymentDetail
{
    private GymSubscriptionPaymentDetailsRepository $repository;
    private Factory $validatorFactory;

    /**
     * @param GymSubscriptionPaymentDetailsRepository $repository
     * @param Factory $validatorFactory
     */
    public function __construct(
        GymSubscriptionPaymentDetailsRepository $repository,
        Factory $validatorFactory
    ) {
        $this->repository = $repository;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @param array $data
     * @return GymSubscriptionPaymentDetail
     * @throws ValidationException
     */
    public function __invoke(array $data): GymSubscriptionPaymentDetail
    {
        $validator = $this->validatorFactory->make($data, $this->rules($data));

        $validator->validate();

        $method = array_key_exists('id', $data) ? 'edit' : 'add';

        return $this->repository->$method($data);
    }

    /**
     * @param $data
     * @return array
     */
    private function rules($data): array
    {
        $rules = [
            'gym_subscription_payment_id' => 'required|numeric',
            'type' => 'required|in:' . implode(
                    ',',
                    collect(GymSubscriptionPaymentDetailType::cases())->pluck('value')->toArray()
                ),
            'gym_subscription_member_id' => 'nullable|numeric',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'amount' => 'required|numeric',
        ];

        if (array_key_exists('id', $data)) {
            $rules['id'] = 'required|exists:gym_subscription_payment_details';
        }

        return $rules;
    }
}
