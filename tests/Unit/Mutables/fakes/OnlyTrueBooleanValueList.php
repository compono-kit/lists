<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes;

use ComponoKit\Types\Lists\Mutables\AbstractBooleanValueList;

class OnlyTrueBooleanValueList extends AbstractBooleanValueList
{
	protected static function isValueValid( bool $value ): bool
	{
		return $value === true;
	}
}
