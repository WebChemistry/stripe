<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Price;

use BackedEnum;
use OutOfBoundsException;
use WebChemistry\Stripe\Resolver\DefaultResolver;

final class DefaultPriceResolver extends DefaultResolver implements PriceResolver
{

	protected string $name = 'Price';

}
