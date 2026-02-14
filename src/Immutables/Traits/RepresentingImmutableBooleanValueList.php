<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanValueList;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanValueList;

trait RepresentingImmutableBooleanValueList
{
	use RepresentingBooleanValueList;

	public function exists( int $index ): bool
	{
		return isset( $this->booleans[ $index ] );
	}

	public function get( int $index ): bool
	{
		$this->guardIndexExists( $index );

		return $this->booleans[ $index ];
	}

	public function set( int $index, bool $value ): static
	{
		$booleans           = $this->booleans;
		$booleans[ $index ] = $value;

		return new static( $booleans );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->booleans;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( bool...$values ): static
	{
		$currentValues = $this->booleans;
		foreach ( $values as $value )
		{
			$currentValues[] = $value;
		}

		return new static( $currentValues );
	}

	public function prepend( bool...$values ): static
	{
		$currentValues = $this->booleans;
		$currentValues = $values + $currentValues;

		return new static( $currentValues );
	}

	public function appendList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): static
	{
		return new static( $this->toNativeType() + ($list instanceof RepresentsBooleanValueList ? $list->toNativeType() : $list->toBooleans()) );
	}

	public function prependList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): static
	{
		return new static( $list instanceof RepresentsBooleanValueList ? $list->toNativeType() : $list->toBooleans() + $this->toNativeType() );
	}
}
