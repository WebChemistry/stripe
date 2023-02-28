<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Utility;

use DateTime;
use DateTimeZone;

final class StripeDateTime
{

	public static function fromTimestamp(int $timestamp): DateTime
	{
		return (new DateTime('@' . $timestamp))->setTimezone(new DateTimeZone(date_default_timezone_get()));
	}

}
