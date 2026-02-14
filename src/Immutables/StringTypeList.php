<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\RepresentsString;

class StringTypeList extends AbstractStringTypeList
{
	protected static function isValueValid( RepresentsString $type ): bool
	{
		return true;
	}
}
