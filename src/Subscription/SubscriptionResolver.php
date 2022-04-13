<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Subscription;

use BackedEnum;

interface SubscriptionResolver
{

	public function resolve(BackedEnum|string $subscription): string;

}
