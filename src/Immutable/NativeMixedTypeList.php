<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable;

class NativeMixedTypeList extends AbstractNativeMixedTypeList
{
	protected static function isValueValid( int|float|string|bool|array $value ): bool
	{
		return true;
	}
}
