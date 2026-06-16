<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

class BooleanValueList extends AbstractBooleanValueList
{
	public static function isValueValid( bool $value ): bool
	{
		return true;
	}
}
