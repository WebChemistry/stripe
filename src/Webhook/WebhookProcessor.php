<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Webhook;

use Psr\Log\LoggerInterface;
use Stripe\Exception\SignatureVerificationException;
use Throwable;
use WebChemistry\Stripe\Bridge\Nette\Webhook\Exception\HeaderIsNotSetException;
use WebChemistry\Stripe\Logger\VoidLogger;

class WebhookProcessor
{

	/**
	 * @param Webhook[] $webhooks
	 */
	public function __construct(
		private array $webhooks,
	)
	{
	}

	/**
	 * @return Webhook[]
	 */
	public function getWebhooks(): array
	{
		return $this->webhooks;
	}

	/**
	 * @param WebhookEventFactory $factory
	 * @param callable(?Webhook $webhook): LoggerInterface|null $loggerFactory
	 * @return int
	 */
	public function process(WebhookEventFactory $factory, ?callable $loggerFactory = null): int
	{
		$loggerFactory ??= fn (): LoggerInterface => new VoidLogger();

		try {
			$event = $factory->create();
		} catch (Throwable $exception) {
			return $this->handleException($exception, $loggerFactory(null));
		}

		$hasError = false;

		foreach ($this->webhooks as $webhook) {
			$logger = $loggerFactory($webhook);

			$events = $webhook->getSubscribedEvents();

			if ($events !== null && !in_array($event->type, $events, true)) {
				continue;
			}

			try {
				$code = $webhook->process($event, $logger);

				if ($code !== $webhook::SUCCESS) {
					$hasError = true;
				}

			} catch (Throwable $exception) {
				$hasError = true;

				$logger->error($exception->getMessage(), [
					'exception' => $exception,
				]);
			}
		}

		return $hasError ? 500 : 200;
	}

	protected function handleException(Throwable $exception, LoggerInterface $logger): int
	{
		if ($exception instanceof HeaderIsNotSetException) {
			$logger->error(sprintf('Header is not set in request with following message: %s', $exception->getMessage()));

			return 403;
		}

		if ($exception instanceof SignatureVerificationException) {
			$logger->error(sprintf('Signature verification problem with following message: %s', $exception->getMessage()));

			return 403;
		}

		$logger->error($exception->getMessage());

		return 500;
	}

}
