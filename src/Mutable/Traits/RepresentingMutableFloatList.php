<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable\Traits;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatList;
use ComponoKit\Types\Lists\Traits\RepresentingFloatList;

trait RepresentingMutableFloatList
{
	use RepresentingFloatList;

	public function append( RepresentsFloat...$types ): void
	{
		foreach ( $types as $type )
		{
			$this->floatTypes[] = $type;
		}
	}

	public function prepend( RepresentsFloat...$types ): void
	{
		$this->floatTypes = $types + $this->floatTypes;
	}

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		unset( $this->floatTypes[ $index ] );
	}

	public function appendList( RepresentsFloatList $list ): void
	{
		$this->floatTypes += $list->toNativeType();
	}

	public function prependList( RepresentsFloatList $list ): void
	{
		$this->floatTypes = $list->toNativeType() + $this->floatTypes;
	}
}
