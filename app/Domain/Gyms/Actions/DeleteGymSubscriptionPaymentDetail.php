<?php

namespace Domain\Gyms\Actions;

use Domain\Gyms\Contracts\Repositories\GymSubscriptionPaymentDetailsRepository;
use Domain\Gyms\Models\GymSubscriptionPaymentDetail;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

class DeleteGymSubscriptionPaymentDetail
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

        return $this->repository->delete($data);
    }

    /**
     * @param $data
     * @return array
     */
    private function rules($data): array
    {
        return [
            'id' => 'required|exists:gym_subscription_payment_details'
        ];
    }
}
