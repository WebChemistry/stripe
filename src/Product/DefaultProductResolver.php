<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Product;

use BackedEnum;
use OutOfBoundsException;
use WebChemistry\Stripe\Resolver\DefaultResolver;

final class DefaultProductResolver extends DefaultResolver implements ProductResolver
{

	protected string $name = 'Product';

}
