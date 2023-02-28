<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Product;

use BackedEnum;
use OutOfBoundsException;

interface ProductResolver
{

	/**
	 * @throws OutOfBoundsException
	 */
	public function parse(string $productId): string;

	/**
	 * @throws OutOfBoundsException
	 */
	public function resolve(string|BackedEnum $product): string;

}
