<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\CustomerPortal;

use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use WebChemistry\Stripe\Customer\StripeCustomer;
use WebChemistry\Stripe\Customer\StripeCustomerFinder;
use WebChemistry\Stripe\Exception\InvalidCustomerIdException;

class DefaultCustomerPortalSessionFactory implements CustomerPortalSessionFactory
{

	public function __construct(
		private StripeClient $stripeClient,
		private StripeCustomerFinder $customerFinder,
	)
	{
	}

	/**
	 * @throws InvalidCustomerIdException
	 * @throws ApiErrorException
	 */
	public function create(StripeCustomer $customer, string $returnUrl): string
	{
		$id = $this->customerFinder->findId($customer);

		if (!$id) {
			throw InvalidCustomerIdException::notSet();
		}

		$session = $this->stripeClient->billingPortal->sessions->create([
			'customer' => $this->customerFinder->findId($customer),
			'return_url' => $returnUrl,
		]);

		return $session->return_url;
	}
	
}
