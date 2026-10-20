<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

class FloatValueList extends AbstractFloatValueList
{
	protected static function isValueValid( float $value ): bool
	{
		return true;
	}
}
