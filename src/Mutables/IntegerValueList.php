<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

class IntegerValueList extends AbstractIntegerValueList
{
	public static function isValueValid( int $value ): bool
	{
		return true;
	}
}
