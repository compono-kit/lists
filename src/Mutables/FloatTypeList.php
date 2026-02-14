<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsFloat;

class FloatTypeList extends AbstractFloatTypeList
{
	public static function isValueValid( RepresentsFloat $type ): bool
	{
		return true;
	}
}
