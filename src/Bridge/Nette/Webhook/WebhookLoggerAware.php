<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Webhook;

use WebChemistry\Stripe\Webhook\WebhookLogger;

interface WebhookLoggerAware
{

	public function setLogger(WebhookLogger $logger): void;

}
