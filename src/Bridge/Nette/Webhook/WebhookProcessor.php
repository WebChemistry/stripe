<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Webhook;

use Stripe\Event;
use WebChemistry\Stripe\Webhook\WebhookLogger;

interface WebhookProcessor
{

	public function process(Event $event, WebhookLogger $logger): void;

}
