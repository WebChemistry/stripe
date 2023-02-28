<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Webhook;

use Psr\Log\LoggerInterface;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Event;
use Stripe\StripeObject;
use Stripe\Subscription;
use WebChemistry\Stripe\Customer\StripeCustomer;
use WebChemistry\Stripe\Model\EntityModel;
use WebChemistry\Stripe\Model\StripeCustomerModel;
use WebChemistry\Stripe\Utility\StripeWebhookUtil;

/**
 * @template TEntity of StripeCustomer
 */
final class CustomerWebhook implements Webhook
{

	/**
	 * @param EntityModel<TEntity> $entityModel
	 */
	public function __construct(
		private EntityModel $entityModel,
		private StripeCustomerModel $stripeCustomerModel,
		private string $linkedKey = 'linked',
	)
	{
	}

	/**
	 * @return string[]
	 */
	public function getSubscribedEvents(): array
	{
		return [
			'checkout.session.completed',
			'customer.subscription.created',
			'customer.deleted',
			'customer.updated',
			'customer.created',
		];
	}

	public function process(Event $event, LoggerInterface $logger): int
	{
		$args = new WebhookArgs($event, $logger);
		$type = $event->type;

		if ($type === 'checkout.session.completed') {
			return $this->processCheckout(StripeWebhookUtil::getObject(Session::class, $event), $args);
		} else if ($type === 'customer.subscription.created') {
			return $this->processSubscription(StripeWebhookUtil::getObject(Subscription::class, $event), $args);
		} else if ($type === 'customer.deleted') {
			return $this->processCustomerDelete(StripeWebhookUtil::getObject(Customer::class, $event), $args);
		} else if ($type === 'customer.updated') {
			return $this->processCustomerUpdate(StripeWebhookUtil::getObject(Customer::class, $event), $args);
		} else if ($type === 'customer.created') {
			return $this->processCustomerCreate(StripeWebhookUtil::getObject(Customer::class, $event), $args);
		}

		return self::SUCCESS;
	}

	private function processCustomerDelete(Customer $customer, WebhookArgs $args): int
	{
		$entity = $this->entityModel->findByCustomer($customer);

		if (!$entity) {
			$args->logger->error(sprintf('Account id not found for customer %s', $customer->id));

			return self::FAILURE;
		}

		$entity->setCustomerId(null);

		$this->entityModel->persist($entity);

		return self::SUCCESS;
	}

	private function processCustomerUpdate(Customer $customer, WebhookArgs $args): int
	{
		return $this->linkCustomerWithEntity($customer, $args);
	}

	private function processCustomerCreate(Customer $customer, WebhookArgs $args): int
	{
		return $this->linkCustomerWithEntity($customer, $args);
	}

	private function processCheckout(Session $session, WebhookArgs $args): int
	{
		$customer = $this->stripeCustomerModel->getNullable($session->customer);

		if (!$customer) {
			return self::SUCCESS;
		}

		$this->passEntityIdToCustomer($customer, $session->metadata, false);
		return $this->linkCustomerWithEntity($customer, $args);
	}

	private function processSubscription(Subscription $subscription, WebhookArgs $args): int
	{
		$customer = $this->stripeCustomerModel->getNullable($subscription->customer);

		if (!$customer) {
			return self::SUCCESS;
		}

		$this->passEntityIdToCustomer($customer, $subscription->metadata, false);
		return $this->linkCustomerWithEntity($customer, $args);
	}

	private function passEntityIdToCustomer(Customer $customer, ?StripeObject $metadata, bool $save = true): void
	{
		if (!$metadata) {
			return;
		}

		$id = $this->entityModel->getStringIdByMetadata($metadata);

		if (!$id) {
			return;
		}

		$this->entityModel->setMetadataEntityId($customer->metadata, $id);

		if ($save) {
			$customer->save();
		}
	}

	private function linkCustomerWithEntity(Customer $customer, WebhookArgs $args, bool $save = true): int
	{
		$metadata = $customer->metadata;

		if (isset($metadata[$this->linkedKey])) {
			return self::SUCCESS;
		}

		$id = $this->entityModel->getStringIdByMetadata($metadata);

		if (!$id) {
			return self::SUCCESS;
		}

		$entity = $this->entityModel->findByCustomerId($customer->id);

		if (!$entity) {
			$entity = $this->entityModel->findByMetadata($metadata);

			if (!$entity) {
				$args->logger->error(sprintf('Cannot find user entity with id %s', $id));

				return self::FAILURE;
			}

			$entity->setCustomerId($customer->id);

			$this->entityModel->persist($entity);
		}

		$metadata[$this->linkedKey] = true;

		if ($save) {
			$customer->save();
		}

		return self::SUCCESS;
	}

}
