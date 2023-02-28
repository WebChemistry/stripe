<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\Checkout;

use InvalidArgumentException;
use Nette\Application\UI\Component;
use Nette\Application\UI\Link;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Stripe\StripeClient\Bridge\Nette;
use WebChemistry\Stripe\Bridge\Nette\Checkout\Exception\InvalidSessionIdException;

final class SuccessCheckout
{

	private const COMPONENT_PARAM = 'sessionId';
	private const PARAM = '{CHECKOUT_SESSION_ID}';
	private const URL_PARAM = '%7BCHECKOUT_SESSION_ID%7D';

	public function __construct(
		private StripeClient $client,
	)
	{
	}

	public function generateLink(Link $link): string
	{
		if (!str_starts_with($link->getDestination(), '//')) {
			throw new InvalidArgumentException(
				sprintf('Given link must be absolute, %s given.', $link->getDestination())
			);
		}
		$parameterId = $link->getComponent()->getParameterId(self::COMPONENT_PARAM);
		$link->setParameter(self::COMPONENT_PARAM, self::PARAM);

		return strtr((string) $link, [
			sprintf('?%s=%s', $parameterId, self::URL_PARAM) => sprintf('?%s=%s', $parameterId, self::PARAM),
			sprintf('&%s=%s', $parameterId, self::URL_PARAM) => sprintf('?%s=%s', $parameterId, self::PARAM),
		]);
	}

	/**
	 * @throws InvalidSessionIdException
	 */
	public function getSession(Component $component): Session
	{
		$parameter = $component->getParameter(self::COMPONENT_PARAM);

		if (!$parameter) {
			throw new InvalidSessionIdException(sprintf('Parameter %s is empty.', self::COMPONENT_PARAM));
		}

		if (!is_string($parameter)) {
			throw new InvalidSessionIdException(sprintf('Parameter %s is not a string.', self::COMPONENT_PARAM));
		}

		try {
			return $this->client->checkout->sessions->retrieve($parameter);
		} catch (ApiErrorException $exception) {
			throw new InvalidSessionIdException($exception->getMessage(), previous: $exception);
		}
	}

	public function getCustomerBySession(Session $session): ?Customer
	{
		if ($session->customer === null) {
			return null;
		}

		if ($session->customer instanceof Customer) {
			return $session->customer;
		}

		try {
			return $this->client->customers->retrieve($session->customer);
		} catch (ApiErrorException) {
			return null;
		}
	}

}
