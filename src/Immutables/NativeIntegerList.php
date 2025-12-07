<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

class NativeIntegerList extends AbstractNativeIntegerList
{
	protected static function isValueValid( int $value ): bool
	{
		return true;
	}
}
