<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Mutables\AbstractStringList;

class AnotherStringList extends AbstractStringList
{
	protected static function isValueValid( RepresentsString $type ): bool
	{
		return true;
	}
}
