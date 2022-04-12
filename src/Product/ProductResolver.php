<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Product;

use BackedEnum;

interface ProductResolver
{

	public function resolve(string|BackedEnum $product): string;

}
