<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes;

use ComponoKit\Types\Lists\Mutables\AbstractIntegerValueList;

class NoTwoIntegerValueList extends AbstractIntegerValueList
{
	protected static function isValueValid( int $value ): bool
	{
		return $value !== 2;
	}
}
