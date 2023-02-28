<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Webhook;

use Psr\Log\LoggerInterface;
use Stripe\Event;

final class WebhookArgs
{

	public function __construct(
		public readonly Event $event,
		public readonly LoggerInterface $logger,
	)
	{
	}

}
