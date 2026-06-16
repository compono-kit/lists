<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes;

use ComponoKit\Types\Lists\Immutables\AbstractBooleanValueList;

class OnlyTrueBooleanValueList extends AbstractBooleanValueList
{
	protected static function isValueValid( bool $value ): bool
	{
		return $value === true;
	}
}
