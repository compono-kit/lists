<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes;

use ComponoKit\Types\Lists\Mutables\AbstractFloatValueList;

class NoTwoFloatValueList extends AbstractFloatValueList
{
	protected static function isValueValid( float $value ): bool
	{
		return $value !== 2.2;
	}
}
