<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes;

use ComponoKit\Types\Lists\Immutables\AbstractStringValueList;

class NoTwoStringValueList extends AbstractStringValueList
{
	protected static function isValueValid( string $value ): bool
	{
		return $value !== 'two';
	}
}
