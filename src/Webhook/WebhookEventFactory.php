<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Webhook;

use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class WebhookEventFactory
{
	
	public function __construct(
		private string $secret,
	)
	{
	}

	/**
	 * @throws SignatureVerificationException
	 */
	public function create(string $body, string $signatureHeader): Event
	{
		return Webhook::constructEvent($body, $signatureHeader, $this->secret);
	}

}
