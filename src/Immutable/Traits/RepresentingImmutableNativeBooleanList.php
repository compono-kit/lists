<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeBooleanList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeBooleanList;

trait RepresentingImmutableNativeBooleanList
{
	use RepresentingNativeBooleanList;

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

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->booleans;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function appendList( RepresentsNativeBooleanList|RepresentsBooleanList $list ): static
	{
		return new static( $this->toNativeType() + ($list instanceof RepresentsNativeBooleanList ? $list->toNativeType() : $list->toBooleans()) );
	}

	public function prependList( RepresentsNativeBooleanList|RepresentsBooleanList $list ): static
	{
		return new static( $list instanceof RepresentsNativeBooleanList ? $list->toNativeType() : $list->toBooleans() + $this->toNativeType() );
	}
}
