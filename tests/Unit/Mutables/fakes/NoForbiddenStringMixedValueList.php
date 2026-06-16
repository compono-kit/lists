<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes;

use ComponoKit\Types\Lists\Mutables\AbstractMixedValueList;

class NoForbiddenStringMixedValueList extends AbstractMixedValueList
{
	protected static function isValueValid( int|float|string|bool|array $value ): bool
	{
		return $value !== 'forbidden';
	}
}
