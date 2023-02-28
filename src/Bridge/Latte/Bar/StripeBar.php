<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Latte\Bar;

use Tracy\Helpers;
use Tracy\IBarPanel;
use WebChemistry\Stripe\Webhook\WebhookProcessor;

final class StripeBar implements IBarPanel
{

	public function __construct(
		private ?WebhookProcessor $webhookProcessor, // @phpstan-ignore-line
	)
	{
	}

	public function getTab(): string
	{
		return Helpers::capture(function (): void {
			require __DIR__ . '/templates/tab.phtml';
		});
	}

	public function getPanel(): string
	{
		return Helpers::capture(function (): void {
			require __DIR__ . '/templates/panel.phtml';
		});
	}

}
