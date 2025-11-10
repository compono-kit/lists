<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeIntegerList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeIntegerList;

trait RepresentingMutableNativeIntegerList
{
	use RepresentingNativeIntegerList;

	public function append( int...$values ): void
	{
		foreach ( $values as $value )
		{
			$this->integers[] = $value;
		}
	}

	public function prepend( int...$values ): void
	{
		$this->integers = $values + $this->integers;
	}

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		unset( $this->integers[ $index ] );
	}

	public function appendList( RepresentsNativeIntegerList|RepresentsIntegerList $list ): void
	{
		$this->integers += $list instanceof RepresentsIntegerList ? $list->toIntegers() : $list->toNativeType();
	}

	public function prependList( RepresentsNativeIntegerList|RepresentsIntegerList $list ): void
	{
		$this->integers = ($list instanceof RepresentsIntegerList ? $list->toIntegers() : $list->toNativeType()) + $this->integers;
	}
}
