<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable;

use ComponoKit\Types\Interfaces\RepresentsInteger;

class IntegerList extends AbstractIntegerList
{
	protected static function isValueValid( RepresentsInteger $type ): bool
	{
		return true;
	}
}
