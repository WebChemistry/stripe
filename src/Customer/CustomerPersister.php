<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Customer;

interface CustomerPersister
{

	public function persist(StripeCustomer $customer): void;

}
