<?php declare(strict_types = 1);

namespace WebChemistry\Stripe\Resolver;

use BackedEnum;
use OutOfBoundsException;

abstract class DefaultResolver
{

	protected string $name = 'Item';

	/**
	 * @param array<string, string> $items
	 */
	public function __construct(
		private array $items,
	)
	{
	}

	public function parse(string $itemId): string
	{
		$key = array_search($itemId, $this->items, true);

		if ($key === false) {
			throw new OutOfBoundsException(
				sprintf('%s %s does not exist.', $this->name, $itemId)
			);
		}

		return (string) $key;
	}

	public function resolve(BackedEnum|string $item): string
	{
		if ($item instanceof BackedEnum) {
			$item = $item->value;
		}

		return $this->items[$item] ?? throw new OutOfBoundsException(
				sprintf('%s %s does not exist.', $this->name, $item)
			);
	}

}
