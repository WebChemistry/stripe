<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Price;

use BackedEnum;
use OutOfBoundsException;

final class DefaultPriceResolver implements PriceResolver
{

	/**
	 * @param array<string, string> $prices
	 */
	public function __construct(
		private array $prices,
	)
	{
	}

	public function resolve(BackedEnum|string $price): string
	{
		if ($price instanceof BackedEnum) {
			$price = $price->value;
		}

		return $this->prices[$price] ?? throw new OutOfBoundsException(sprintf('Price %s does not exist.', $price));
	}

}
