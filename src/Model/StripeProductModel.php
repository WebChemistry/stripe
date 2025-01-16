<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Model;

use Stripe\Collection;
use Stripe\Plan;
use Stripe\StripeClient;
use Stripe\Subscription;
use WebChemistry\Stripe\Model\Resource\CustomerProduct;
use WebChemistry\Stripe\Utility\StripeIdExtractor;

final class StripeProductModel
{

	public function __construct(
		private StripeClient $stripeClient,
	)
	{
	}

	/**
	 * @return CustomerProduct[]
	 */
	public function getAllActiveByCustomer(string $customerId, bool $includeTrialing = true, bool $reportMissingProduct = false): array
	{
		$subscriptions = $this->stripeClient->subscriptions->all([
			'customer' => $customerId,
			'status' => $includeTrialing ? 'all' : 'active',
			'limit' => 100,
		]);

		$products = [];

		foreach ($this->processSubscriptions($subscriptions) as $product) {
			$products[] = $product;
		}

		return $products;
	}

	/**
	 * @param Collection<Subscription> $subscriptions
	 * @return iterable<CustomerProduct>
	 */
	private function processSubscriptions(Collection $subscriptions, bool $reportMissingProduct = false): iterable
	{
		/** @var Subscription $subscription */
		foreach ($subscriptions->autoPagingIterator() as $subscription) {
			if (!in_array($subscription->status, ['active', 'trialing'], true)) {
				continue;
			}

			foreach ($this->getPlans($subscription) as $plan) {
				$product = StripeIdExtractor::extractNullable($plan->product);

				if (!$product) {
					if ($reportMissingProduct) {
						trigger_error(
							sprintf(
								'Product id does not exist for plan %s and customer %s',
								$plan->id,
								StripeIdExtractor::extract($subscription->customer),
							)
						);
					}

					continue;
				}

				yield new CustomerProduct(
					$product,
					$subscription,
				);
			}
		}
	}

	/**
	 * @return Plan[]
	 */
	private function getPlans(Subscription $subscription): array
	{
		$plans = [];

		foreach ($subscription->items->autoPagingIterator() as $item) {
			$plans[] = $item->plan;
		}

		return $plans;
	}

}
