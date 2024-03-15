<?php

namespace Domain\Gyms\Contracts\Services;

use Domain\Gyms\Actions\DeleteGymFeeType;
use Domain\Gyms\Actions\DeleteGymSubscription;
use Domain\Gyms\Actions\DeleteGymSubscriptionMember;
use Domain\Gyms\Actions\DeleteGymSubscriptionMemberAccess;
use Domain\Gyms\Actions\DeleteGymSubscriptionMemberAccessRight;
use Domain\Gyms\Actions\DeleteGymSubscriptionPayment;
use Domain\Gyms\Actions\DeleteGymSubscriptionPaymentDetail;
use Domain\Gyms\Actions\DeleteGymSubscriptionVersion;
use Domain\Gyms\DataTransferObjects\GymFeeTypeEntity;
use Domain\Gyms\DataTransferObjects\GymFeeTypeSearchRequest;
use Domain\Gyms\DataTransferObjects\GymFeeTypeSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessRightEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessRightSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessRightSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionNoteEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionNoteSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionNoteSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentDetailEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentDetailSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentDetailSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionVersionEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionVersionSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionVersionSearchResponse;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

interface GymsService
{
    /**
     * @param array $data
     * @return GymFeeTypeEntity|null
     */
    public function createGymFeeType(array $data): ?GymFeeTypeEntity;

    /**
     * @param array $data
     * @return GymSubscriptionEntity|null
     */
    public function createGymSubscription(array $data): ?GymSubscriptionEntity;

    /**
     * @param array $data
     * @return GymSubscriptionMemberEntity|null
     */
    public function createGymSubscriptionMember(array $data): ?GymSubscriptionMemberEntity;

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessEntity|null
     */
    public function createGymSubscriptionMemberAccess(array $data): ?GymSubscriptionMemberAccessEntity;

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessRightEntity|null
     */
    public function createGymSubscriptionMemberAccessRight(array $data): ?GymSubscriptionMemberAccessRightEntity;

    /**
     * @param array $data
     * @return GymSubscriptionNoteEntity|null
     */
    public function createGymSubscriptionNote(array $data): ?GymSubscriptionNoteEntity;

    /**
     * @param array $data
     * @return GymSubscriptionPaymentEntity|null
     */
    public function createGymSubscriptionPayment(array $data, array $includes = []): ?GymSubscriptionPaymentEntity;

    /**
     * @param array $data
     * @return GymSubscriptionPaymentDetailEntity|null
     */
    public function createGymSubscriptionPaymentDetail(array $data, array $includes = []): ?GymSubscriptionPaymentDetailEntity;

    /**
     * @param array $data
     * @return GymSubscriptionVersionEntity|null
     */
    public function createGymSubscriptionVersion(array $data): ?GymSubscriptionVersionEntity;

    /**
     * @param array $data
     * @return GymFeeTypeEntity                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     w
     */
    public function deleteGymFeeType(array $data): GymFeeTypeEntity;

    /**
     * @param array $data
     * @return GymSubscriptionEntity
     */
    public function deleteGymSubscription(array $data): GymSubscriptionEntity;

    /**
     * @param array $data
     * @return GymSubscriptionMemberEntity
     * @throws UnknownProperties
     */
    public function deleteGymSubscriptionMember(array $data): GymSubscriptionMemberEntity;

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessEntity
     */
    public function deleteGymSubscriptionMemberAccess(array $data): GymSubscriptionMemberAccessEntity;

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessRightEntity
     */
    public function deleteGymSubscriptionMemberAccessRight(array $data): GymSubscriptionMemberAccessRightEntity;

    /**
     * @param array $data
     * @return GymSubscriptionNoteEntity|null
     */
    public function deleteGymSubscriptionNote(array $data): ?GymSubscriptionNoteEntity;

    /**
     * @param array $data
     * @return GymSubscriptionPaymentEntity
     */
    public function deleteGymSubscriptionPayment(array $data): GymSubscriptionPaymentEntity;

    /**
     * @param array $data
     * @return GymSubscriptionPaymentDetailEntity
     */
    public function deleteGymSubscriptionPaymentDetail(array $data): GymSubscriptionPaymentDetailEntity;

    /**
     * @param array $data
     * @return GymSubscriptionVersionEntity
     */
    public function deleteGymSubscriptionVersion(array $data): GymSubscriptionVersionEntity;

    /**
     * @param int $id
     * @param array $includes
     * @return GymFeeTypeEntity|null
     */
    public function findGymFeeType(int $id, array $includes = []): ?GymFeeTypeEntity;

    /**
     * @param int $id
     * @param array $includes
     * @return GymSubscriptionEntity|null
     */
    public function findGymSubscription(int $id, array $includes = []): ?GymSubscriptionEntity;

    /**
     * @param int $id
     * @param array $includes
     * @return GymSubscriptionMemberEntity|null
     */
    public function findGymSubscriptionMember(int $id, array $includes = []): ?GymSubscriptionMemberEntity;

    /**
     * @param int $id
     * @param array $includes
     * @return GymSubscriptionMemberAccessEntity|null
     */
    public function findGymSubscriptionMemberAccess(int $id, array $includes = []): ?GymSubscriptionMemberAccessEntity;

    /**
     * @param int $id
     * @param array $includes
     * @return GymSubscriptionMemberAccessRightEntity|null
     */
    public function findGymSubscriptionMemberAccessRight(
        int $id,
        array $includes = []
    ): ?GymSubscriptionMemberAccessRightEntity;

    /**
     * @param int $id
     * @param array $includes
     * @return GymSubscriptionNoteEntity|null
     */
    public function findGymSubscriptionNote(int $id, array $includes = []): ?GymSubscriptionNoteEntity;

    /**
     * @param int $id
     * @param array $includes
     * @return GymSubscriptionPaymentEntity|null
     */
    public function findGymSubscriptionPayment(int $id, array $includes = []): ?GymSubscriptionPaymentEntity;

    /**
     * @param int $id
     * @param array $includes
     * @return GymSubscriptionPaymentDetailEntity|null
     */
    public function findGymSubscriptionPaymentDetail(
        int $id,
        array $includes = []
    ): ?GymSubscriptionPaymentDetailEntity;

    /**
     * @param integer $id
     * @return GymSubscriptionEntity
     */
    public function payQuota(int $id): GymSubscriptionEntity;

    /**
     * @param int $id
     * @param array $includes
     * @return GymSubscriptionVersionEntity|null
     */
    public function findGymSubscriptionVersion(int $id, array $includes = []): ?GymSubscriptionVersionEntity;

    /**
     * @param array $data
     * @return GymSubscriptionEntity
     */
    public function makeVersion(array $data): GymSubscriptionEntity;

    /**
     * @param GymFeeTypeSearchRequest $request
     * @return GymFeeTypeSearchResponse
     */
    public function searchGymFeeTypes(GymFeeTypeSearchRequest $request): GymFeeTypeSearchResponse;

    /**
     * @param GymSubscriptionMemberAccessSearchRequest $request
     * @return GymSubscriptionMemberAccessSearchResponse
     */
    public function searchGymSubscriptionMemberAccess(
        GymSubscriptionMemberAccessSearchRequest $request
    ): GymSubscriptionMemberAccessSearchResponse;

    /**
     * @param GymSubscriptionMemberAccessRightSearchRequest $request
     * @return GymSubscriptionMemberAccessRightSearchResponse
     */
    public function searchGymSubscriptionMemberAccessRights(
        GymSubscriptionMemberAccessRightSearchRequest $request
    ): GymSubscriptionMemberAccessRightSearchResponse;

    /**
     * @param GymSubscriptionMemberSearchRequest $request
     * @return GymSubscriptionMemberSearchResponse
     */
    public function searchGymSubscriptionMembers(
        GymSubscriptionMemberSearchRequest $request
    ): GymSubscriptionMemberSearchResponse;
    
    /**
     * @param GymSubscriptionMemberSearchRequest $request
     * @return GymSubscriptionMemberSearchResponse
     */
    public function searchGymSubscriptionNotes(GymSubscriptionNoteSearchRequest $request
    ): GymSubscriptionNoteSearchResponse;

    /**
     * @param GymSubscriptionPaymentDetailSearchRequest $request
     * @return GymSubscriptionPaymentDetailSearchResponse
     */
    public function searchGymSubscriptionPaymentDetails(
        GymSubscriptionPaymentDetailSearchRequest $request
    ): GymSubscriptionPaymentDetailSearchResponse;

    /**
     * @param GymSubscriptionPaymentSearchRequest $request
     * @return GymSubscriptionPaymentSearchResponse
     */
    public function searchGymSubscriptionPayments(
        GymSubscriptionPaymentSearchRequest $request
    ): GymSubscriptionPaymentSearchResponse;

    /**
     * @param GymSubscriptionVersionSearchRequest $request
     * @return GymSubscriptionVersionSearchResponse
     */
    public function searchGymSubscriptionVersion(GymSubscriptionVersionSearchRequest $request): GymSubscriptionVersionSearchResponse;

    /**
     * @param GymSubscriptionSearchRequest $request
     * @return GymSubscriptionSearchResponse
     */
    public function searchGymSubscriptions(GymSubscriptionSearchRequest $request): GymSubscriptionSearchResponse;

    /**
     * @param array $data
     * @return GymFeeTypeEntity|null
     */
    public function updateGymFeeType(array $data): ?GymFeeTypeEntity;

    /**
     * @param array $data
     * @return GymSubscriptionEntity|null
     */
    public function updateGymSubscription(array $data): ?GymSubscriptionEntity;

    /**
     * @param array $data
     * @return GymSubscriptionMemberEntity|null
     */
    public function updateGymSubscriptionMember(array $data): ?GymSubscriptionMemberEntity;

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessEntity|null
     */
    public function updateGymSubscriptionMemberAccess(array $data): ?GymSubscriptionMemberAccessEntity;

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessRightEntity|null
     */
    public function updateGymSubscriptionMemberAccessRight(array $data): ?GymSubscriptionMemberAccessRightEntity;

    /**
     * @param array $data
     * @return GymSubscriptionNoteEntity|null
     */
    public function updateGymSubscriptionNote(array $data): ?GymSubscriptionNoteEntity;

    /**
     * @param array $data
     * @return GymSubscriptionPaymentEntity|null
     */
    public function updateGymSubscriptionPayment(array $data, array $includes = []): ?GymSubscriptionPaymentEntity;

    /**
     * @param array $data
     * @return GymSubscriptionPaymentDetailEntity|null
     */
    public function updateGymSubscriptionPaymentDetail(array $data, array $includes = []): ?GymSubscriptionPaymentDetailEntity;

    /**
     * @param array $data
     * @return GymSubscriptionVersionEntity|null
     */
    public function updateGymSubscriptionVersion(array $data): ?GymSubscriptionVersionEntity;
}
