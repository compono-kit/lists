<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsFloatList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeFloatList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeFloatList;

trait RepresentingImmutableNativeFloatList
{
	use RepresentingNativeFloatList;

	public function exists( int $index ): bool
	{
		return isset( $this->floats[ $index ] );
	}

	public function get( int $index ): float
	{
		$this->guardIndexExists( $index );

		return $this->floats[ $index ];
	}

	public function set( int $index, float $value ): static
	{
		$floats           = $this->floats;
		$floats[ $index ] = $value;

		return new self( $floats );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->floats;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( float...$values ): static
	{
		$currentValues = $this->floats;
		foreach ( $values as $value )
		{
			$currentValues[] = $value;
		}

		return new static( $currentValues );
	}

	public function prepend( float...$values ): static
	{
		$currentValues = $this->floats;
		$currentValues = $values + $currentValues;

		return new static( $currentValues );
	}

	public function appendList( RepresentsNativeFloatList|RepresentsFloatList $list ): static
	{
		return new static( $this->toNativeType() + ($list instanceof RepresentsNativeFloatList ? $list->toNativeType() : $list->toFloats()) );
	}

	public function prependList( RepresentsNativeFloatList|RepresentsFloatList $list ): static
	{
		return new static( $list instanceof RepresentsNativeFloatList ? $list->toNativeType() : $list->toFloats() + $this->toNativeType() );
	}
}
