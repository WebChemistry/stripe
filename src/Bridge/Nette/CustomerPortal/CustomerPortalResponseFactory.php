<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\CustomerPortal;

use Nette\Application\Responses\RedirectResponse;
use Stripe\Exception\ApiErrorException;
use WebChemistry\Stripe\Customer\StripeCustomer;
use WebChemistry\Stripe\CustomerPortal\CustomerPortalSessionFactory;
use WebChemistry\Stripe\Exception\InvalidCustomerIdException;

final class CustomerPortalResponseFactory
{

	public function __construct(
		private CustomerPortalSessionFactory $customerPortalSessionFactory,
	)
	{
	}

	/**
	 * @throws ApiErrorException
	 * @throws InvalidCustomerIdException
	 */
	public function create(StripeCustomer $customer, string $returnUrl): RedirectResponse
	{
		return new RedirectResponse($this->customerPortalSessionFactory->create($customer, $returnUrl), 303);
	}

}
