<?php

namespace Domain\Gyms\Actions;

use Domain\Gyms\Contracts\Repositories\GymSubscriptionPaymentsRepository;
use Domain\Gyms\Models\GymSubscriptionPayment;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

class DeleteGymSubscriptionPayment
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
     * @throws ValidationException
     */
    public function __invoke(array $data): GymSubscriptionPayment
    {
        $validator = $this->validatorFactory->make($data, $this->rules($data));

        $validator->validate();

        return $this->repository->delete($data);
    }

    /**
     * @param $data
     * @return array
     */
    private function rules($data): array
    {
        return [
            'id' => 'required|exists:gym_subscription_payments'
        ];
    }
}
