<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\DI;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Utils\Arrays;
use stdClass;
use Stripe\StripeClient;
use WebChemistry\Stripe\Bridge\Nette\CustomerPortal\CustomerPortalResponseFactory;
use WebChemistry\Stripe\Customer\DefaultStripeCustomerFinder;
use WebChemistry\Stripe\Customer\StripeCustomerFinder;
use WebChemistry\Stripe\CustomerPortal\CustomerPortalSessionFactory;
use WebChemistry\Stripe\CustomerPortal\DefaultCustomerPortalSessionFactory;
use WebChemistry\Stripe\Price\DefaultPriceResolver;
use WebChemistry\Stripe\Price\PriceResolver;
use WebChemistry\Stripe\Product\DefaultProductResolver;
use WebChemistry\Stripe\Product\ProductResolver;
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
				'webhook' => Expect::string()->required(),
			]),
			'products' => Expect::arrayOf(Expect::structure([
				'test' => Expect::string(),
				'live' => Expect::string(),
			])),
			'prices' => Expect::arrayOf(Expect::structure([
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
			->setFactory(WebhookEventFactory::class, [$config->keys->webhook]);

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

		$builder->addDefinition($this->prefix('priceResolver'))
			->setType(PriceResolver::class)
			->setFactory(
				DefaultPriceResolver::class,
				[Arrays::map($config->prices, fn (stdClass $price) => $price->$environment)]
			);
	}

}
