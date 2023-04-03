<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Model\Resource;

use DateTime;
use Stripe\Subscription;
use WebChemistry\Stripe\Utility\StripeDateTime;

final class CustomerProduct
{

	public function __construct(
		private string $productId,
		private Subscription $subscription,
	)
	{
	}

	public function getProductId(): string
	{
		return $this->productId;
	}

	public function getStatus(): string
	{
		return $this->subscription->status;
	}

	public function getPeriodEnd(): DateTime
	{
		return StripeDateTime::fromTimestamp($this->subscription->current_period_end);
	}

	public function getPeriodStart(): DateTime
	{
		return StripeDateTime::fromTimestamp($this->subscription->current_period_start);
	}

	public function getTrialPeriodEnd(): ?DateTime
	{
		if ($this->subscription->status !== 'trialing') {
			return null;
		}

		return $this->getPeriodEnd();
	}

	public function getTrialPeriodStart(): ?DateTime
	{
		if ($this->subscription->status !== 'trialing') {
			return null;
		}

		return $this->getPeriodStart();
	}

	public function isTrial(): bool
	{
		return $this->subscription->status === 'trialing';
	}

}
