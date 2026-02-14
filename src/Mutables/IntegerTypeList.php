<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsInteger;

class IntegerTypeList extends AbstractIntegerTypeList
{
	protected static function isValueValid( RepresentsInteger $type ): bool
	{
		return true;
	}
}
