<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

class FloatValueList extends AbstractFloatValueList
{
	protected static function isValueValid( float $value ): bool
	{
		return true;
	}
}
