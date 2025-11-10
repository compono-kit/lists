<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable;

class NativeIntegerList extends AbstractNativeIntegerList
{
	public static function isValueValid( int $value ): bool
	{
		return true;
	}
}
