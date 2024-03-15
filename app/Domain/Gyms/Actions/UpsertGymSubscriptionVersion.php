<?php

namespace Domain\Gyms\Actions;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;
use Domain\Gyms\Enums\GymSubscriptionPaymentType;
use Domain\Gyms\Models\GymSubscriptionVersion;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Domain\Gyms\Contracts\Repositories\GymSubscriptionVersionRepository;

class UpsertGymSubscriptionVersion
{
    private GymSubscriptionVersionRepository $repository;
    private Factory $validatorFactory;

    /**
     * @param GymSubscriptionVersionRepository $repository
     * @param Factory $validatorFactory
     */
    public function __construct(
        GymSubscriptionVersionRepository $repository,
        Factory $validatorFactory
    ) {
        $this->repository = $repository;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @param array $data
     * @return GymSubscriptionVersion
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function __invoke(array $data): GymSubscriptionVersion
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
            'client_id' => 'required|numeric',
            'gym_fee_type_id' => 'required|numeric',
            'gym_fee_type_name' => 'required|max:255',
            'duration_number_of_days' => 'required|numeric',
            'price' => 'required|numeric',
            'price_beneficiaries' => 'nullable|numeric',
            'activation_date' => 'required',
            'start_date' => 'required',
            'end_date' => 'nullable',
            'expiration_date' => 'required',
            'payment_day' => 'nullable|numeric',
            'payment_type' => 'required|in:' . implode(',', collect(GymSubscriptionPaymentType::cases())->pluck('value')->toArray()),
            'gym_subscription_id' => 'required|numeric',
        ];

        if (array_key_exists('id', $data)) {
            $rules['id'] = 'required|exists:gym_subscription_versions';
        }

        return $rules;
    }
}
