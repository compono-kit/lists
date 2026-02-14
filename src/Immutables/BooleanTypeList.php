<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\RepresentsBoolean;

class BooleanTypeList extends AbstractBooleanTypeList
{
	protected static function isValueValid( RepresentsBoolean $type ): bool
	{
		return true;
	}
}
