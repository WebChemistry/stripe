<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\CustomerPortal;

use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use WebChemistry\Stripe\Customer\StripeCustomer;
use WebChemistry\Stripe\Exception\InvalidCustomerIdException;

class DefaultCustomerPortalSessionFactory implements CustomerPortalSessionFactory
{

	public function __construct(
		private StripeClient $stripeClient,
	)
	{
	}

	/**
	 * @throws InvalidCustomerIdException
	 * @throws ApiErrorException
	 */
	public function create(StripeCustomer|Customer $customer, string $returnUrl): string
	{
		$id = $customer instanceof StripeCustomer ? $customer->getCustomerId() : $customer->id;
		if (!$id) {
			throw InvalidCustomerIdException::notSet();
		}

		$session = $this->stripeClient->billingPortal->sessions->create([
			'customer' => $id,
			'return_url' => $returnUrl,
		]);

		return $session->url;
	}
	
}
