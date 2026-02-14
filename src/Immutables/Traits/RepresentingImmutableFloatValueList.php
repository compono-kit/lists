<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatValueList;
use ComponoKit\Types\Lists\Traits\RepresentingFloatValueList;

trait RepresentingImmutableFloatValueList
{
	use RepresentingFloatValueList;

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

		return new static( $floats );
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

	public function appendList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): static
	{
		return new static( $this->toNativeType() + ($list instanceof RepresentsFloatValueList ? $list->toNativeType() : $list->toFloats()) );
	}

	public function prependList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): static
	{
		return new static( $list instanceof RepresentsFloatValueList ? $list->toNativeType() : $list->toFloats() + $this->toNativeType() );
	}
}
