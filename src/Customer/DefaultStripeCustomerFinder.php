<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Customer;

use Stripe\StripeClient;

class DefaultStripeCustomerFinder implements StripeCustomerFinder
{

	public function __construct(
		private StripeClient $client,
		private ?StripeCustomerPersister $persister,
	)
	{
	}

	public function findId(StripeCustomer $customer): ?string
	{
		if ($id = $customer->getCustomerId()) {
			return $id;
		}

		$result = $this->client->customers->all([
			'email' => $customer->getEmail(),
			'limit' => 1,
		])->first();

		if ($result === null) {
			return null;
		}

		$customer->setCustomerId($result->id);

		$this->persister?->update($customer);

		return $result->id;
	}

}
