<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerValueList;

trait RepresentingImmutableIntegerValueList
{
	use RepresentingIntegerValueList;

	public function exists( int $index ): bool
	{
		return isset( $this->integers[ $index ] );
	}

	public function get( int $index ): int
	{
		$this->guardIndexExists( $index );

		return $this->integers[ $index ];
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->integers;
		array_splice( $values, $index, 1 );

		return new static( ...$values );
	}

	public function append( int...$values ): static
	{
		return new static( ...$this->integers, ...$values );
	}

	public function prepend( int...$values ): static
	{
		return new static( ...$values, ...$this->integers );
	}

	public function appendList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...($list instanceof RepresentsIntegerValueList ? $list->toNativeType() : $list->toIntegers()) );
	}

	public function prependList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): static
	{
		return new static( ...($list instanceof RepresentsIntegerValueList ? $list->toNativeType() : $list->toIntegers()), ...$this->toNativeType() );
	}
}
