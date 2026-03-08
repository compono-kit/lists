<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Immutables\AbstractMixedTypeList;

class NoFloatTypeList extends AbstractMixedTypeList
{
	protected static function isValueValid( RepresentsType $type ): bool
	{
		return !($type instanceof RepresentsFloat);
	}
}
