<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Utility;

use Stripe\Customer;
use Stripe\Product;
use Stripe\Subscription;

final class StripeIdExtractor
{

	public static function extract(Customer|Subscription|Product|string $resource): string
	{
		return match (true) {
			is_string($resource) => $resource,
			$resource instanceof Customer => $resource->id,
			$resource instanceof Product => $resource->id,
			$resource instanceof Subscription => $resource->id,
		};
	}

	public static function extractNullable(Customer|Subscription|Product|string|null $resource): ?string
	{
		if ($resource === null) {
			return null;
		}

		return self::extract($resource);
	}

}
