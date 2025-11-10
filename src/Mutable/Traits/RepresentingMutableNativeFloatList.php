<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsFloatList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeFloatList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeFloatList;

trait RepresentingMutableNativeFloatList
{
	use RepresentingNativeFloatList;

	public function append( float...$values ): void
	{
		foreach ( $values as $value )
		{
			$this->floats[] = $value;
		}
	}

	public function prepend( float...$values ): void
	{
		$this->floats = $values + $this->floats;
	}

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		unset( $this->floats[ $index ] );
	}

	public function appendList( RepresentsNativeFloatList|RepresentsFloatList $list ): void
	{
		$this->floats += $list instanceof RepresentsFloatList ? $list->toFloats() : $list->toNativeType();
	}

	public function prependList( RepresentsNativeFloatList|RepresentsFloatList $list ): void
	{
		$this->floats = ($list instanceof RepresentsFloatList ? $list->toFloats() : $list->toNativeType()) + $this->floats;
	}
}
