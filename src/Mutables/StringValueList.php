<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

class StringValueList extends AbstractStringValueList
{
	protected static function isValueValid( string $value ): bool
	{
		return true;
	}
}
