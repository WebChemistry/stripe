<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Model;

use Stripe\Customer;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

final class StripeCustomerModel
{

	public function __construct(
		private StripeClient $stripeClient,
	)
	{
	}

	public function get(Customer|string $customer): Customer
	{
		if (is_string($customer)) {
			return $this->stripeClient->customers->retrieve($customer);
		}

		return $customer;
	}

	public function getNullable(Customer|string|null $customer, bool $notFoundAsNull = false): ?Customer
	{
		if ($customer === null) {
			return null;
		}

		try {
			return $this->get($customer);
		} catch (InvalidRequestException $exception) {
			if ($notFoundAsNull && $exception->getHttpStatus() === 404) {
				return null;
			}

			throw $exception;
		}
	}

}
