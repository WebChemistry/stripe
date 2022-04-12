<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Product;

use BackedEnum;
use OutOfBoundsException;

final class DefaultProductResolver implements ProductResolver
{

	/**
	 * @param array<string, string> $products
	 */
	public function __construct(
		private array $products,
	)
	{
	}

	public function resolve(BackedEnum|string $product): string
	{
		if ($product instanceof BackedEnum) {
			$product = $product->value;
		}

		return $this->products[$product] ?? throw new OutOfBoundsException(sprintf('Product %s does not exist.', $product));
	}

}
