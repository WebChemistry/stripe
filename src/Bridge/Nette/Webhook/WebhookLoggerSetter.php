<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Webhook;

use WebChemistry\Stripe\Webhook\WebhookLogger;

trait WebhookLoggerSetter
{

	private WebhookLogger $logger;

	public function setLogger(WebhookLogger $logger): void
	{
		$this->logger = $logger;
	}

	public function getLogger(): WebhookLogger
	{
		return $this->logger;
	}

}
