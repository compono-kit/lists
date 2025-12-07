<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsType;

class MixedTypeList extends AbstractMixedTypeList
{
	protected static function isValueValid( RepresentsType $type ): bool
	{
		return true;
	}
}
