<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Price;

use BackedEnum;
use OutOfBoundsException;

interface PriceResolver
{

	/**
	 * @throws OutOfBoundsException
	 */
	public function parse(string $priceId): string;

	/**
	 * @throws OutOfBoundsException
	 */
	public function resolve(BackedEnum|string $price): string;

}
