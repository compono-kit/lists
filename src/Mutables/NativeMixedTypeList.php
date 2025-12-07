<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

class NativeMixedTypeList extends AbstractNativeMixedTypeList
{
	protected static function isValueValid( string $value ): bool
	{
		return true;
	}
}
