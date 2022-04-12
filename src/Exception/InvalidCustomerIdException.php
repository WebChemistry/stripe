<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Exception;

final class InvalidCustomerIdException extends \Exception
{

	public static function notSet(): self
	{
		return new self('Customer ID is not set.');
	}

}
