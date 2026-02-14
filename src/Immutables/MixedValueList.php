<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

class MixedValueList extends AbstractMixedValueList
{
	protected static function isValueValid( int|float|string|bool|array $value ): bool
	{
		return true;
	}
}
