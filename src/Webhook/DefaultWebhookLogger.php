<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Webhook;

final class DefaultWebhookLogger implements WebhookLogger
{

	/** @var array<int, string> */
	private array $logs = [];

	public function __construct(
		private ?string $name = null,
	)
	{
	}

	public function log(string $message): void
	{
		$message = $this->name ? sprintf('%s: %s', $this->name, $message) : $message;

		$this->logs[] = $message;
	}

	public function getLogs(): array
	{
		return $this->logs;
	}

}
