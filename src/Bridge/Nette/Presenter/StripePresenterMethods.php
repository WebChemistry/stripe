<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Presenter;

use Nette\DI\Attributes\Inject;
use Stripe\Exception\SignatureVerificationException;
use Throwable;
use Tracy\ILogger;
use WebChemistry\Stripe\Bridge\Nette\Webhook\Exception\HeaderIsNotSetException;
use WebChemistry\Stripe\Bridge\Nette\Webhook\WebhookEventFactory;
use WebChemistry\Stripe\Bridge\Nette\Webhook\WebhookProcessor;
use WebChemistry\Stripe\Bridge\Nette\Webhook\WebhookProcessorCollection;
use WebChemistry\Stripe\Exception\WebhookException;

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
			$this->logger?->log($exception, Logger::ERROR);

			$this->sendError($exception->getMessage(), 403);
		}

		$errors = [];
		$error = false;
		foreach ($this->processors->getProcessors() as $processor) {
			try {
				$processor->process($event);
			} catch(WebhookException $exception) {
				$errors[] = $exception->getMessage();
			} catch (Throwable $exception) {
				$this->logger?->log($exception, Logger::ERROR);

				$error = true;
			}
		}

		if ($errors) {
			$this->sendError($errors, 500);
		} else if ($error) {
			$this->sendError('Internal error, see logs.', 500);
		}

		$this->sendJson([
			'status' => 'success',
		]);
	}

	/**
	 * @param string|string[] $message
	 * @return never
	 */
	private function sendError(string|array $message, int $code): void
	{
		$this->getHttpResponse()->setCode($code);

		$this->sendJson([
			'error' => $message,
		]);
	}

}
