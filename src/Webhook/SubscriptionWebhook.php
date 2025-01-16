<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Webhook;

use Psr\Log\LoggerInterface;
use Stripe\Event;
use Stripe\Subscription;
use WebChemistry\Stripe\Customer\StripeCustomer;
use WebChemistry\Stripe\Model\EntityModel;
use WebChemistry\Stripe\Model\Resource\CustomerProduct;
use WebChemistry\Stripe\Model\Resource\CustomerProductCollection;
use WebChemistry\Stripe\Model\StripeProductModel;
use WebChemistry\Stripe\Product\ProductResolver;
use WebChemistry\Stripe\Utility\StripeWebhookUtil;

/**
 * @template TEntity of StripeCustomer
 */
abstract class SubscriptionWebhook implements Webhook
{

	protected const AcceptAnyProduct = '';

	/**
	 * @param EntityModel<TEntity> $entityModel
	 */
	public function __construct(
		protected EntityModel $entityModel,
		protected StripeProductModel $stripeProductModel,
		protected ProductResolver $productResolver,
	)
	{
	}

	/**
	 * @return string[]
	 */
	public function getSubscribedEvents(): array
	{
		return ['customer.subscription.created', 'customer.subscription.deleted', 'customer.subscription.updated'];
	}

	/**
	 * @return array<string, callable(TEntity, ?CustomerProduct, WebhookArgs): int>
	 */
	abstract protected function getCallbacks(): array;

	public function process(Event $event, LoggerInterface $logger): int
	{
		$subscription = StripeWebhookUtil::getObject(Subscription::class, $event);

		/** @var TEntity|null $entity */
		$entity = $this->getEntity($subscription);

		if (!$entity) {
			$logger->error(sprintf('Customer not found for subscription.'));

			return self::FAILURE;
		}

		$collection = new CustomerProductCollection(
			$this->stripeProductModel->getAllActiveByCustomer($this->getCustomerId($subscription, $entity)),
		);

		$state = self::SUCCESS;

		foreach ($this->getCallbacks() as $product => $callback) {
			$customerProduct = $collection->getMostImportant(
				$product === self::AcceptAnyProduct ? null : $this->productResolver->resolve($product),
			);

			$code = $callback($entity, $customerProduct, new WebhookArgs($event, $logger));

			if (is_int($code) && $code !== self::SUCCESS) {
				$state = $code;
			}
		}

		return $state;
	}

	private function getEntity(Subscription $subscription): ?StripeCustomer
	{
		if ($entity = $this->entityModel->findByCustomerId($this->getCustomerIdBySubscription($subscription))) {
			return $entity;
		}

		return $this->entityModel->findByMetadata($subscription->metadata);
	}

	private function getCustomerId(Subscription $subscription, StripeCustomer $entity): string
	{
		return $entity->getCustomerId() ?? $this->getCustomerIdBySubscription($subscription);
	}

	private function getCustomerIdBySubscription(Subscription $subscription): string
	{
		$customer = $subscription->customer;

		return is_string($customer) ? $customer : $customer->id;
	}

}
