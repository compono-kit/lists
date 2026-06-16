<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes;

use ComponoKit\Types\Lists\Immutables\AbstractIntegerValueList;

class NoTwoIntegerValueList extends AbstractIntegerValueList
{
	protected static function isValueValid( int $value ): bool
	{
		return $value !== 2;
	}
}
