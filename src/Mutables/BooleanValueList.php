<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

class BooleanValueList extends AbstractBooleanValueList
{
	protected static function isValueValid( bool $value ): bool
	{
		return true;
	}
}
