<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Webhook;

use Nette\Http\IRequest;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use WebChemistry\Stripe\Bridge\Nette\Webhook\Exception\BodyIsEmptyException;
use WebChemistry\Stripe\Bridge\Nette\Webhook\Exception\HeaderIsNotSetException;
use WebChemistry\Stripe\Webhook\WebhookEventFactory;

final class RequestWebhookEventFactory implements WebhookEventFactory
{

	public function __construct(
		private string $secret,
		private IRequest $request,
	)
	{
	}

	/**
	 * @throws SignatureVerificationException
	 * @throws HeaderIsNotSetException
	 * @throws BodyIsEmptyException
	 */
	public function create(): Event
	{
		$header = $this->request->getHeader('stripe-signature');

		if (!is_string($header)) {
			throw new HeaderIsNotSetException('Required header stripe-signature is not set.');
		}

		$body = $this->request->getRawBody();

		if (!$body) {
			throw new BodyIsEmptyException();
		}

		return Webhook::constructEvent($body, $header, $this->secret);
	}

}
