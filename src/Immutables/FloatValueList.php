<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

class FloatValueList extends AbstractFloatValueList
{
	public static function isValueValid( float $value ): bool
	{
		return true;
	}
}
