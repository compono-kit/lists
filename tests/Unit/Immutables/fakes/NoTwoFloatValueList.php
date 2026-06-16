<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes;

use ComponoKit\Types\Lists\Immutables\AbstractFloatValueList;

class NoTwoFloatValueList extends AbstractFloatValueList
{
	protected static function isValueValid( float $value ): bool
	{
		return $value !== 2.2;
	}
}
