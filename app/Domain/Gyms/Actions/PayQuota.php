<?php

namespace Domain\Gyms\Actions;

use Carbon\Carbon;
use Domain\Gyms\Contracts\Services\GymsService;
use Domain\Gyms\DataTransferObjects\GymSubscriptionEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberSearchResponse;
use Domain\Orders\Contracts\Services\OrdersService;
use Domain\Orders\DataTransferObjects\OrderEntity;
use Domain\Orders\Enums\OrderType;
use Domain\Orders\Enums\Source;
use Domain\Payments\Enums\PaymentType;
use Domain\Products\Contracts\Services\ProductsService;
use Domain\Products\DataTransferObjects\ProductEntity;
use Domain\Products\Enums\PriceType;
use Exception;

class PayQuota
{
    /**
     * @var GymsService
     */
    private GymsService $gymsService;

    /**
     * @var OrdersService
     */
    private OrdersService $ordersService;

    /**
     * @var ProductsService
     */
    private ProductsService $productsService;

    /**
     * @param OrdersService $ordersService
     * @param ProductsService $productsService
     * @param GymsService $gymsService
     */
    public function __construct(
        OrdersService $ordersService,
        ProductsService $productsService,
        GymsService $gymsService,
    ) {
        $this->ordersService = $ordersService;
        $this->productsService = $productsService;
        $this->gymsService = $gymsService;
    }

    /**
     * @param integer $id
     * @return GymSubscriptionEntity
     * @throws Exception
     */
    public function __invoke(int $id): GymSubscriptionEntity
    {
        $subscription = $this->gymsService->findGymSubscription($id);

        if ($subscription !== null) {

            $subscriptionMembers = $this->gymsService->searchGymSubscriptionMembers(
                new GymSubscriptionMemberSearchRequest([
                    'filters' => ['gym_subscription_id' => $subscription->id]
                ])
            );

            $price = $this->calculateSubscriptionPrice($subscription, $subscriptionMembers);

            $product = $this->getOrCreateProduct('cuota gimnasio');

            $this->createOrder($subscription, $price, $product);

            return $this->extendSubscriptionExpirationDate($subscription);
        } else {
            throw new Exception('Gym subscription not found');
        }
    }

    /**
     * @param GymSubscriptionEntity $subscription
     * @param [type] $subscriptionMembers
     * @return float
     */
    private function calculateSubscriptionPrice(GymSubscriptionEntity $subscription, $subscriptionMembers): float
    {
        $cantSubscriptionMembers = $subscriptionMembers->getData()->count();

        return $subscription->price + $subscription->price_beneficiaries * $cantSubscriptionMembers;
    }

    /**
     * @param string $productName
     * @return ProductEntity
     */
    private function getOrCreateProduct(string $productName): ProductEntity
    {
        $product = $this->productsService->findByName($productName);

        if (!$product instanceof ProductEntity) {
            $product = $this->createProduct($productName);
        }

        return $product;
    }

    /**
     * @param string $productName
     * @return ProductEntity
     */
    private function createProduct(string $productName): ProductEntity
    {
        $product = [
            'product_type_id' => 286,
            'priority' => 1,
            'price' => 0,
            'price_type' => PriceType::FIXED->value,
            'circuit_sessions' => 0,
            'treatment_sessions' => 0,
            'online_sale' => true,
            'editable' => true,
            'available' => true,
            'name' => $productName,
            'all_reserves_on_same_day' => false,
            'active' => 1,
            'background_color' => '#FFFFFF',
            'text_color' => '#000000'
        ];

        return $this->productsService->create($product);
    }

    /**
     * @param GymSubscriptionEntity $subscription
     * @param float $price
     * @param ProductEntity $product
     * @return OrderEntity
     */
    private function createOrder(GymSubscriptionEntity $subscription, float $price, ProductEntity $product): OrderEntity
    {
        $order = [
            'client_id' => $subscription->client_id,
            'company_id' => 1,
            'source' => Source::CRM->value,
            'total_price' => $price,
            'type' => OrderType::CLIENT->value,
            'details' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'quantity' => 1,
                ],
            ],
            'payments' => [
                [
                    'amount' => $price,
                    'paid_amount' => $price,
                    'returned_amount' => '0',
                    'type' => PaymentType::CASH->value,
                ],
            ],
        ];

        return $this->ordersService->create($order);
    }

    /**
     * @param GymSubscriptionEntity $subscription
     * @return GymSubscriptionEntity|null
     */
    private function extendSubscriptionExpirationDate(GymSubscriptionEntity $subscription): ?GymSubscriptionEntity
    {
        $expirationDate = Carbon::parse($subscription->expiration_date)
            ->addDays($subscription->duration_number_of_days)
            ->toDateString();


        $gymSubscription = [
            'id' => $subscription->id,
            'expiration_date' => $expirationDate,
            'client_id' => $subscription->client_id,
            'gym_fee_type_id' => $subscription->gym_fee_type_id,
            'gym_fee_type_name' => $subscription->gym_fee_type_name,
            'duration_number_of_days' => $subscription->duration_number_of_days,
            'price' => $subscription->price,
            'activation_date' => $subscription->activation_date,
            'start_date' => $subscription->start_date,
            'payment_day' => $subscription->payment_day,
            'payment_type' => $subscription->payment_type,
        ];

        return $this->gymsService->updateGymSubscription($gymSubscription);
    }
}
