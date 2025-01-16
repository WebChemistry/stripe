<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Model\Resource;

final class CustomerProductCollection
{

	/**
	 * @param CustomerProduct[] $products
	 */
	public function __construct(
		private array $products,
	)
	{
	}

	/**
	 * Returns in this order: non-trial with the highest period end, trial with the highest period end
	 */
	public function getMostImportant(?string $productId = null): ?CustomerProduct
	{
		$return = null;

		foreach ($this->products as $product) {
			if ($productId !== null && $product->getProductId() !== $productId) {
				continue;
			}

			if ($return === null) {
				$return = $product;
			} else if ($return->isTrial() && !$product->isTrial()) {
				$return = $product;
			} else if ($product->getTrialPeriodEnd() > $return->getTrialPeriodEnd()) {
				$return = $product;
			}
		}

		return $return;
	}

}
