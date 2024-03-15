<?php

namespace Domain\Festives\Actions;

use Domain\Festives\Contracts\Repositories\FestivesRepository;
use Domain\Festives\Models\Festive;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UpsertFestive
{
    private FestivesRepository $repository;
    private Factory $validatorFactory;

    /**
     * @param FestivesRepository $repository
     * @param Factory $validatorFactory
     */
    public function __construct(
        FestivesRepository $repository,
        Factory $validatorFactory
    ) {
        $this->repository = $repository;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @param array $data
     * @return Festive
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function __invoke(array $data): Festive
    {
        $validator = $this->validatorFactory->make($data, $this->rules($data));

        $validator->validate();

        $method = array_key_exists('id', $data) ? 'edit' : 'add';

        if ($method === 'edit' && empty($data['closed_hours']) && !$data['working_hours_as_sunday']) {
            return $this->repository->delete($data);
        }

        return $this->repository->$method($data);
    }

    /**
     * @param $data
     * @return array
     */
    private function rules($data): array
    {
        $rules = [
            'date' => 'required|date_format:Y-m-d|unique:festives,date',
            'type' => 'required|in:Día Completo,Horas',
            'closed_hours' => [
                Rule::requiredIf(function () use ($data) {
                    return (request()->get('type') === 'Horas'
                        && empty(request()->get('id'))
                        && !$data['working_hours_as_sunday']
                    );
                }),
                'array'
            ],
            'closed_hours.*' => 'date_format:H:i'
        ];

        if (array_key_exists('id', $data)) {
            $rules['id'] = 'required|exists:festives';
            $rules['date'] .= ',' . $data['id'];
        }

        return $rules;
    }
}
