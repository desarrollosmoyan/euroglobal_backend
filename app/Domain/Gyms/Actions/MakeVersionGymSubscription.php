<?php

namespace Domain\Gyms\Actions;

use Carbon\Carbon;
use Domain\Gyms\Contracts\Repositories\GymSubscriptionsRepository;
use Domain\Gyms\Contracts\Services\GymsService;
use Domain\Gyms\Models\GymSubscription;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

class MakeVersionGymSubscription
{
    private GymSubscriptionsRepository $repository;
    private GymsService $service;
    private Factory $validatorFactory;

    /**
     * @param GymSubscriptionsRepository $repository
     * @param Factory $validatorFactory
     */
    public function __construct(
        GymSubscriptionsRepository $repository,
        Factory $validatorFactory,
        GymsService $service
    ) {
        $this->repository = $repository;
        $this->validatorFactory = $validatorFactory;
        $this->service = $service;
    }

    /**
     * @param array $data
     * @return GymSubscription
     * @throws ValidationException
     */
    public function __invoke(array $data): GymSubscription
    {
        $validator = $this->validatorFactory->make($data, $this->rules());

        $validator->validate();

        $subscription = $this->repository->find($data['id']);


        if ($subscription->gym_fee_type_id !== $data['gym_fee_type_id']) {
            $gymFeeType = $this->service->findGymFeeType($data['gym_fee_type_id']);

            $data['gym_fee_type_name'] = $gymFeeType->name;
            $data['duration_number_of_days'] = $gymFeeType->duration_number_of_days;
            $data['price'] = $gymFeeType->price;
            $data['price_beneficiaries'] = $gymFeeType->price_beneficiaries;
            $data['payment_day'] = $gymFeeType->payment_day ?? $subscription->payment_day;
            $data['biweekly_payment_day'] = $gymFeeType->biweekly_payment_day;

            $expirationDate = Carbon::parse($subscription->expiration_date)
                ->addDays($gymFeeType->duration_number_of_days)
                ->toDateString();
            $data['expiration_date'] = $expirationDate;
        }

        return $this->repository->edit($data);
    }

    /**
     * @return array
     */
    private function rules(): array
    {
        return [
            'id' => 'required|exists:gym_subscriptions,id',
            'start_date' => 'required',
            'gym_fee_type_id' => 'required|numeric',
        ];
    }
}
