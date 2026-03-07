<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Lists\Immutables\AbstractFloatTypeList;

class NoTwoFloatTypeList extends AbstractFloatTypeList
{
	protected static function isValueValid( RepresentsFloat $type ): bool
	{
		return $type->toFloat() !== 2.0;
	}
}
