<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable;

class NativeFloatList extends AbstractNativeFloatList
{
	public static function isValueValid( float $value ): bool
	{
		return true;
	}
}
