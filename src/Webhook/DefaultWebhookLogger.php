<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Webhook;

final class DefaultWebhookLogger implements WebhookLogger
{

	/** @var array<int, string> */
	private array $logs = [];

	private ?string $name;

	public function applyName(?string $name = null): void
	{
		$this->name = $name;
	}

	public function log(string $message): void
	{
		$message = $this->name ? sprintf('%s: %s', $this->name, $message) : $message;

		$this->logs[] = $message;
	}

	/**
	 * @return array<int, string>
	 */
	public function getLogs(): array
	{
		return $this->logs;
	}

}
