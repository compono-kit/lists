<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes;

use ComponoKit\Types\Lists\Immutables\AbstractMixedValueList;

class NoIntegerMixedValueList extends AbstractMixedValueList
{
	protected static function isValueValid( int|float|string|bool|array $value ): bool
	{
		return !is_int( $value );
	}
}
