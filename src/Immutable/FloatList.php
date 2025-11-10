<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable;

use ComponoKit\Types\Interfaces\RepresentsFloat;

class FloatList extends AbstractFloatList
{
	public static function isValueValid( RepresentsFloat $type ): bool
	{
		return true;
	}
}
