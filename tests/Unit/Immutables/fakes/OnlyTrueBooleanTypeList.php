<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Immutables\AbstractBooleanTypeList;

class OnlyTrueBooleanTypeList extends AbstractBooleanTypeList
{
	protected static function isValueValid( RepresentsBoolean $type ): bool
	{
		return $type->toBoolean() === true;
	}
}
