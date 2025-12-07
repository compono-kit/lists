<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

class NativeFloatList extends AbstractNativeFloatList
{
	public static function isValueValid( float $value ): bool
	{
		return true;
	}
}
