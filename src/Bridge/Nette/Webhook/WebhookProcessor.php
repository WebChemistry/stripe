<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Webhook;

use Stripe\Event;

interface WebhookProcessor
{

	public function process(Event $event): void;

}
