<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

class IntegerValueList extends AbstractIntegerValueList
{
	protected static function isValueValid( int $value ): bool
	{
		return true;
	}
}
