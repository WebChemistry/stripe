<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Webhook;

use WebChemistry\Stripe\Webhook\WebhookProcessor;

final class WebhookProcessorCollection
{

	/**
	 * @param WebhookProcessor[] $processors
	 */
	public function __construct(
		private array $processors,
	)
	{
	}

	/**
	 * @return WebhookProcessor[]
	 */
	public function getProcessors(): array
	{
		return $this->processors;
	}

}
