<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Customer;

interface StripeCustomerPersister
{

	public function update(StripeCustomer $customer): void;

}
