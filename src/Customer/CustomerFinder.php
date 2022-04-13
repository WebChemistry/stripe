<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Customer;

use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use WebChemistry\Stripe\Exception\NoResultException;

final class CustomerFinder
{

	/** @var Customer[] */
	private array $cache = [];

	public function __construct(
		private StripeClient $client,
		private CustomerPersister $persister,
	)
	{
	}

	/**
	 * @param mixed[] $options
	 * @throws ApiErrorException
	 */
	public function retrieve(StripeCustomer $customer, array $options): Customer
	{
		try {
			return $this->find($customer);
		} catch (NoResultException) {
			$options['metadata']['id'] = $customer->getId();

			$result = $this->client->customers->create($options);

			$customer->setCustomerId($result->id);

			$this->persister->persist($customer);

			return $this->cache[$result->id] = $result;
		}
	}

	/**
	 * @throws ApiErrorException
	 * @throws NoResultException
	 */
	public function find(StripeCustomer $customer): Customer
	{
		$id = $customer->getCustomerId();

		if (isset($this->cache[$id])) {
			return $this->cache[$id];
		}

		if ($id) {
			try {
				return $this->cache[$id] = $this->client->customers->retrieve($id);
			} catch (ApiErrorException $exception) {
				if ($exception->getHttpStatus() !== 404) {
					throw $exception;
				}

				$customer->setCustomerId(null);

				$this->persister->persist($customer);
			}
		}

		$result = $this->client->customers->search([
			'query' => "metadata['id']: '{$customer->getId()}'",
		])->first();

		if (!$result instanceof Customer) {
			throw new NoResultException('Customer not found.');
		}

		$customer->setCustomerId($result->id);

		$this->persister->persist($customer);

		return $this->cache[$result->id] = $result;
	}

}
