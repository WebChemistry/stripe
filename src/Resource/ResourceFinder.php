<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Resource;

use InvalidArgumentException;
use LogicException;
use Stripe\ApiResource;
use Stripe\Customer;
use Stripe\StripeClient;

final class ResourceFinder
{

	public function __construct(
		private StripeClient $client,
	)
	{
	}

	public function getId(ApiResource|string $resource): string
	{
		return is_string($resource) ? $resource : $resource->id;
	}

	/**
	 * @template T of object
	 * @param ApiResource|string $resource
	 * @param class-string<T> $className
	 * @return T
	 */
	public function find(ApiResource|string $resource, string $className): object
	{
		if (is_string($resource)) {
			return match ($className) {
				Customer::class => $this->client->customers->retrieve($resource),
				default => throw new LogicException(sprintf('Api resource %s is not currently supported.', $className)),
			};
		} elseif ($resource instanceof $className) {
			return $resource;
		}

		throw new InvalidArgumentException(
			sprintf('Api resource is not instance of %s, %s given.', $className, get_debug_type($resource))
		);
	}

}
