<?php

namespace Domain\Gyms\Actions;

use Domain\Gyms\Contracts\Repositories\GymSubscriptionNotesRepository;
use Domain\Gyms\Models\GymSubscriptionNote;
use Illuminate\Contracts\Validation\Factory;



class UpsertGymSubscriptionNote
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
     * @return GymSubscriptionNote
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function __invoke(array $data): GymSubscriptionNote
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
            'gym_subscription_id' => 'required|exists:gym_subscriptions,id',
            'note' => 'required'
        ];

        if (array_key_exists('id', $data)) {
            $rules['id'] = 'required|exists:gym_subscription_notes';
        }

        return $rules;
    }
}
