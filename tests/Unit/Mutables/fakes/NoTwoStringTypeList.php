<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Mutables\AbstractStringTypeList;

class NoTwoStringTypeList extends AbstractStringTypeList
{
	protected static function isValueValid( RepresentsString $type ): bool
	{
		return !str_contains( $type->toString(), '2' );
	}
}
