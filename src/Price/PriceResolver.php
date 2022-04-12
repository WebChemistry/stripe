<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Price;

use BackedEnum;

interface PriceResolver
{

	public function resolve(BackedEnum|string $price): string;

}
