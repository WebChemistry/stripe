<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Bridge\Nette\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Utils\Arrays;
use stdClass;
use Stripe\StripeClient;
use WebChemistry\Stripe\Bridge\Nette\CustomerPortal\CustomerPortalResponseFactory;
use WebChemistry\Stripe\Bridge\Nette\Webhook\WebhookLoggerAware;
use WebChemistry\Stripe\Bridge\Nette\Webhook\WebhookProcessorCollection;
use WebChemistry\Stripe\Customer\DefaultStripeCustomerFinder;
use WebChemistry\Stripe\Customer\StripeCustomerFinder;
use WebChemistry\Stripe\CustomerPortal\CustomerPortalSessionFactory;
use WebChemistry\Stripe\CustomerPortal\DefaultCustomerPortalSessionFactory;
use WebChemistry\Stripe\Price\DefaultPriceResolver;
use WebChemistry\Stripe\Price\PriceResolver;
use WebChemistry\Stripe\Product\DefaultProductResolver;
use WebChemistry\Stripe\Product\ProductResolver;
use WebChemistry\Stripe\Subscription\DefaultSubscriptionResolver;
use WebChemistry\Stripe\Subscription\SubscriptionResolver;
use WebChemistry\Stripe\Webhook\DefaultWebhookLogger;
use WebChemistry\Stripe\Webhook\WebhookEventFactory;

final class StripeExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'environment' => Expect::string('live'),
			'keys' => Expect::structure([
				'secret' => Expect::string()->required(),
				'public' => Expect::string()->required(),
				'webhook' => Expect::string(),
			]),
			'products' => Expect::arrayOf(Expect::structure([
				'test' => Expect::string(),
				'live' => Expect::string(),
			])),
			'prices' => Expect::arrayOf(Expect::structure([
				'test' => Expect::string(),
				'live' => Expect::string(),
			])),
			'subscriptions' => Expect::arrayOf(Expect::structure([
				'test' => Expect::string(),
				'live' => Expect::string(),
			])),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var stdClass $config */
		$config = $this->getConfig();
		$environment = $config->environment;

		$builder->addDefinition($this->prefix('client'))
			->setFactory(StripeClient::class, [$config->keys->secret]);

		$builder->addDefinition($this->prefix('webhookFactory'))
			->setFactory(WebhookEventFactory::class, [(string) $config->keys->webhook]);

		$builder->addDefinition($this->prefix('netteWebhookFactory'))
			->setFactory(\WebChemistry\Stripe\Bridge\Nette\Webhook\WebhookEventFactory::class, [(string) $config->keys->webhook]);

		$builder->addDefinition($this->prefix('processorCollection'))
			->setFactory(WebhookProcessorCollection::class);

		$builder->addDefinition($this->prefix('customerFinder'))
			->setType(StripeCustomerFinder::class)
			->setFactory(DefaultStripeCustomerFinder::class);

		$builder->addDefinition($this->prefix('customerPortal'))
			->setType(CustomerPortalSessionFactory::class)
			->setFactory(DefaultCustomerPortalSessionFactory::class);

		$builder->addDefinition($this->prefix('customerPortalResponse'))
			->setFactory(CustomerPortalResponseFactory::class);

		$builder->addDefinition($this->prefix('productResolver'))
			->setType(ProductResolver::class)
			->setFactory(
				DefaultProductResolver::class,
				[Arrays::map($config->products, fn (stdClass $product) => $product->$environment)]
			);

		$builder->addDefinition($this->prefix('subscriptionResolver'))
			->setType(SubscriptionResolver::class)
			->setFactory(
				DefaultSubscriptionResolver::class,
				[Arrays::map($config->subscriptions, fn (stdClass $subscription) => $subscription->$environment)]
			);

		$builder->addDefinition($this->prefix('priceResolver'))
			->setType(PriceResolver::class)
			->setFactory(
				DefaultPriceResolver::class,
				[Arrays::map($config->prices, fn (stdClass $price) => $price->$environment)]
			);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		foreach ($builder->findByType(WebhookLoggerAware::class) as $webhook) {
			if ($webhook instanceof ServiceDefinition) {
				$webhook->addSetup('setLogger', [new Statement(DefaultWebhookLogger::class, ['@self'])]);
			}
		}
	}

}
