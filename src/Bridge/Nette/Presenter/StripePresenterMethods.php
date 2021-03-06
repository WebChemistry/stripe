<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Presenter;

use Stripe\Exception\SignatureVerificationException;
use Throwable;
use Tracy\ILogger;
use WebChemistry\Stripe\Bridge\Nette\Webhook\Exception\HeaderIsNotSetException;
use WebChemistry\Stripe\Bridge\Nette\Webhook\WebhookEventFactory;
use WebChemistry\Stripe\Bridge\Nette\Webhook\WebhookProcessor;
use WebChemistry\Stripe\Bridge\Nette\Webhook\WebhookProcessorCollection;
use WebChemistry\Stripe\Exception\WebhookException;
use WebChemistry\Stripe\Webhook\DefaultWebhookLogger;

trait StripePresenterMethods
{

	public WebhookEventFactory $webhookEventFactory;

	public ?ILogger $logger;

	public WebhookProcessorCollection $processors;

	/**
	 * @param WebhookProcessor[] $processors
	 */
	final public function injectStripePresenterMethods(
		WebhookEventFactory $webhookEventFactory,
		?ILogger $logger,
		WebhookProcessorCollection $webhookProcessorCollection,
	): void
	{
		$this->webhookEventFactory = $webhookEventFactory;
		$this->logger = $logger;
		$this->processors = $webhookProcessorCollection;
	}

	/**
	 * @never
	 */
	public function processWebhooks(): void
	{
		try {
			$event = $this->webhookEventFactory->create();
		} catch (HeaderIsNotSetException $exception) {
			$this->sendError($exception->getMessage(), 403);
		} catch (SignatureVerificationException $exception) {
			$this->sendError($exception->getMessage(), 403);
		} catch (Throwable $exception) {
			$this->logger?->log($exception, ILogger::ERROR);

			$this->sendError($exception->getMessage(), 403);
		}

		$errors = [];
		$logs = [];
		$logger = new DefaultWebhookLogger();
		foreach ($this->processors->getProcessors() as $processor) {
			$logger->applyName($processor::class);

			try {
				$processor->process($event, $logger);
			} catch(WebhookException $exception) {
				$errors[] = sprintf('Webhook %s: %s', $processor::class, $exception->getMessage());
			} catch (Throwable $exception) {
				$this->logger?->log($exception, ILogger::ERROR);
				$errors[] = sprintf('Webhook %s: %s', $processor::class, $exception->getMessage());
			}

			$logs = array_merge($logs, $logger->getLogs());
		}

		if ($errors) {
			$this->sendError($errors, 500, $logs);
		}

		$this->sendJson([
			'status' => 'success',
			'logs' => $logs,
		]);
	}

	/**
	 * @param string|string[] $message
	 * @param string[] $logs
	 * @return never
	 */
	private function sendError(string|array $message, int $code, array $logs = []): void
	{
		$this->getHttpResponse()->setCode($code);

		$this->sendJson([
			'error' => $message,
			'logs' => $logs,
		]);
	}

}
