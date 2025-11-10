<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable;

use ComponoKit\Types\Interfaces\RepresentsString;

class StringList extends AbstractStringList
{
	protected static function isValueValid( RepresentsString $type ): bool
	{
		return true;
	}
}
