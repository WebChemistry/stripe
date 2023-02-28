<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Utility;

use LogicException;
use Stripe\Event;

final class StripeWebhookUtil
{

	/**
	 * @template T of object
	 * @param class-string<T> $className
	 * @param Event $event
	 * @return T
	 */
	public static function getObject(string $className, Event $event): object
	{
		$object = $event->data->object ?? throw new LogicException('Cannot get object from stripe event.');

		if (!$object instanceof $className) {
			throw new LogicException(sprintf('Class %s is not instance of %s', $object::class, $className));
		}

		return $object;
	}

}
