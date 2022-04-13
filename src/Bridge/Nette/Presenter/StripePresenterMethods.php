<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Presenter;

use Nette\DI\Attributes\Inject;
use Stripe\Exception\SignatureVerificationException;
use Throwable;
use Tracy\ILogger;
use Tracy\Logger;
use WebChemistry\Stripe\Bridge\Nette\Webhook\Exception\HeaderIsNotSetException;
use WebChemistry\Stripe\Bridge\Nette\Webhook\WebhookEventFactory;
use WebChemistry\Stripe\Bridge\Nette\Webhook\WebhookProcessor;

trait StripePresenterMethods
{

	#[Inject]
	public WebhookEventFactory $webhookEventFactory;

	#[Inject]
	public ?ILogger $logger;

	/** @var WebhookProcessor[] */
	#[Inject]
	public array $processors = [];

	public function processWebhooks(): void
	{
		try {
			$event = $this->webhookEventFactory->create();
		} catch (HeaderIsNotSetException $exception) {
			$this->sendError($exception->getMessage(), 403);
		} catch (SignatureVerificationException $exception) {
			$this->sendError($exception->getMessage(), 403);
		} catch (Throwable $exception) {
			$this->logger?->log($exception, Logger::ERROR);

			$this->sendError($exception->getMessage(), 403);
		}

		$error = null;
		foreach ($this->processors as $processor) {
			try {
				$processor->process($event);
			} catch (Throwable $exception) {
				$this->logger?->log($exception, Logger::ERROR);

				$error = $exception->getMessage();
			}
		}

		if ($error !== null) {
			$this->sendError($error, 500);
		}

		$this->sendJson([
			'status' => 'success',
		]);
	}

	/**
	 * @return never
	 */
	private function sendError(string $message, int $code): void
	{
		$this->getHttpResponse()->setCode($code);

		$this->sendJson([
			'error' => $message,
		]);
	}

}
