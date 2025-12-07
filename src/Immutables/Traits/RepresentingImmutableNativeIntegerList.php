<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeIntegerList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeIntegerList;

trait RepresentingImmutableNativeIntegerList
{
	use RepresentingNativeIntegerList;

	public function exists( int $index ): bool
	{
		return isset( $this->integers[ $index ] );
	}

	public function get( int $index ): int
	{
		$this->guardIndexExists( $index );

		return $this->integers[ $index ];
	}

	public function set( int $index, int $value ): static
	{
		$integers           = $this->integers;
		$integers[ $index ] = $value;

		return new self( $integers );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->integers;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( int...$values ): static
	{
		$currentValues = $this->integers;
		foreach ( $values as $value )
		{
			$currentValues[] = $value;
		}

		return new static( $currentValues );
	}

	public function prepend( int...$values ): static
	{
		$currentValues = $this->integers;
		$currentValues = $values + $currentValues;

		return new static( $currentValues );
	}

	public function appendList( RepresentsNativeIntegerList|RepresentsIntegerList $list ): static
	{
		return new static( $this->toNativeType() + ($list instanceof RepresentsNativeIntegerList ? $list->toNativeType() : $list->toIntegers()) );
	}

	public function prependList( RepresentsNativeIntegerList|RepresentsIntegerList $list ): static
	{
		return new static( $list instanceof RepresentsNativeIntegerList ? $list->toNativeType() : $list->toIntegers() + $this->toNativeType() );
	}
}
