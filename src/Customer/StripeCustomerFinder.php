<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Customer;

interface StripeCustomerFinder
{

	public function findId(StripeCustomer $customer): ?string;

}
