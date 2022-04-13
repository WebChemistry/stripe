<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Subscription;

use WebChemistry\Stripe\Resolver\DefaultResolver;

final class DefaultSubscriptionResolver extends DefaultResolver
{

	protected string $name = 'Subscription';

}
