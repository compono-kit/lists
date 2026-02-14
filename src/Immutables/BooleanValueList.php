<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Immutables\Traits\RepresentingImmutableBooleanValueList;

class BooleanValueList extends AbstractBooleanValueList
{
	use RepresentingImmutableBooleanValueList;

	public static function isValueValid( bool $value ): bool
	{
		return true;
	}
}
