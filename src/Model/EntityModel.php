<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Model;

use Stripe\Customer;
use Stripe\StripeObject;
use WebChemistry\Stripe\Customer\StripeCustomer;

/**
 * @template TEntity of StripeCustomer
 */
interface EntityModel
{

	/**
	 * @return TEntity|null
	 */
	public function findByCustomerId(string $customerId): ?StripeCustomer;

	/**
	 * @return TEntity|null
	 */
	public function findByCustomer(Customer $customer): ?StripeCustomer;

	/**
	 * @return TEntity|null
	 */
	public function findByMetadata(StripeObject $metadata): ?StripeCustomer;

	public function getStringIdByMetadata(StripeObject $metadata): ?string;

	public function setMetadataEntityId(StripeObject $metadata, string $id): void;

	/**
	 * @param TEntity $entity
	 */
	public function persist(StripeCustomer $entity): void;

}
