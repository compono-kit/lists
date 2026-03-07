<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Immutables\AbstractIntegerTypeList;

class NoTwoIntegerTypeList extends AbstractIntegerTypeList
{
	protected static function isValueValid( RepresentsInteger $type ): bool
	{
		return $type->toInteger() !== 2;
	}
}
