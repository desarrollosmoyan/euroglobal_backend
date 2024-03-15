<?php

namespace Domain\Gyms\Actions;

use Domain\Gyms\Contracts\Repositories\GymSubscriptionNotesRepository;
use Domain\Gyms\Models\GymSubscriptionNote;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

class DeleteGymSubscriptionNote
{
    private GymSubscriptionNotesRepository $repository;
    private Factory $validatorFactory;

    /**
     * @param GymSubscriptionNotesRepository $repository
     * @param Factory $validatorFactory
     */
    public function __construct(
        GymSubscriptionNotesRepository $repository,
        Factory $validatorFactory
    ) {
        $this->repository = $repository;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @param array $data
     * @return GymSubscription
     * @throws ValidationException
     */
    public function __invoke(array $data): GymSubscriptionNote
    {
        $validator = $this->validatorFactory->make($data, $this->rules());

        $validator->validate();

        return $this->repository->delete($data);
    }

    /**
     * @return array
     */
    private function rules(): array
    {
        return [
            'id' => 'required|exists:gym_subscription_notes'
        ];
    }
}
