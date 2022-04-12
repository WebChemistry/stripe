<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Webhook;

use Nette\Http\IRequest;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use WebChemistry\Stripe\Bridge\Nette\Webhook\Exception\BodyIsEmptyException;
use WebChemistry\Stripe\Bridge\Nette\Webhook\Exception\HeaderIsNotSetException;
use WebChemistry\Stripe\Webhook\WebhookEventFactory as WebhookEventFactoryDecorated;

final class WebhookEventFactory
{

	private WebhookEventFactoryDecorated $factory;

	public function __construct(
		string $secret,
		private IRequest $request,
	)
	{
		$this->factory = new WebhookEventFactoryDecorated($secret);
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

		return $this->factory->create($body, $header);
	}

}
