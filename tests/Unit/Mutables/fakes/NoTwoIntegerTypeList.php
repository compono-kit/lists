<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Mutables\AbstractIntegerTypeList;

class NoTwoIntegerTypeList extends AbstractIntegerTypeList
{
	protected static function isValueValid( RepresentsInteger $type ): bool
	{
		return $type->toInteger() !== 2;
	}
}
