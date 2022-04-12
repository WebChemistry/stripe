<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Customer;

interface StripeCustomer
{

	public function getEmail(): string;

	public function getCustomerId(): ?string;

	/**
	 * @return static|void
	 */
	public function setCustomerId(?string $customerId);

}
