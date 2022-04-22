<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Checkout;

use Nette\Application\UI\Link;

interface SuccessCheckoutFactory
{

	public function create(): SuccessCheckout;

}
