<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable;

class NativeStringList extends AbstractNativeStringList
{
	protected static function isValueValid( string $value ): bool
	{
		return true;
	}
}
