<?php

namespace Domain\Gyms\Actions;

use Domain\Gyms\Models\GymSubscriptionVersion;
use Domain\Gyms\Contracts\Repositories\GymSubscriptionVersionRepository;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

class DeleteGymSubscriptionVersion
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
     * @throws ValidationException
     */
    public function __invoke(array $data): GymSubscriptionVersion
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
            'id' => 'required|exists:gym_subscription_versions'
        ];
    }
}
