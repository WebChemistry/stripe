<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Webhook;

use Stripe\Event;

interface WebhookEventFactory
{

	public function create(): Event;

}
