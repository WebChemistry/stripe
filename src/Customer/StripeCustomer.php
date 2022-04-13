<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Customer;

interface StripeCustomer
{

	/**
	 * @return string|int
	 */
	public function getId();

	public function getCustomerId(): ?string;

	/**
	 * @return static|void
	 */
	public function setCustomerId(?string $customerId);

}
