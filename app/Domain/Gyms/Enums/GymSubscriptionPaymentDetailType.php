<?php

namespace Domain\Gyms\Enums;

enum GymSubscriptionPaymentDetailType: string
{
    case QUOTA = 'quota';
    case MEMBER = 'member';
    case CARD = 'card';
}
