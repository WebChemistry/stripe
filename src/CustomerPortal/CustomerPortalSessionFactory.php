<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\CustomerPortal;

use Stripe\Exception\ApiErrorException;
use WebChemistry\Stripe\Customer\StripeCustomer;
use WebChemistry\Stripe\Exception\InvalidCustomerIdException;

interface CustomerPortalSessionFactory
{

	/**
	 * @throws InvalidCustomerIdException
	 * @throws ApiErrorException
	 */
	public function create(StripeCustomer $customer, string $returnUrl): string;

}
