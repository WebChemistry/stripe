<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Webhook;

interface WebhookLogger
{

	public function log(string $message): void;

}
