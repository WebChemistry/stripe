<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Webhook;

use Psr\Log\LoggerInterface;
use Stripe\Event;

interface Webhook
{

	public const SUCCESS = 0;
	public const FAILURE = 1;

	/**
	 * @return string[]|null
	 */
	public function getSubscribedEvents(): ?array;

	public function process(Event $event, LoggerInterface $logger): int;

}
