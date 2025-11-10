<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsNativeStringList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeStringList;

trait RepresentingImmutableNativeStringList
{
	use RepresentingNativeStringList;

	public function append( string...$values ): static
	{
		$currentValues = $this->strings;
		foreach ( $values as $value )
		{
			$currentValues[] = $value;
		}

		return new static( $currentValues );
	}

	public function prepend( string...$values ): static
	{
		$currentValues = $this->strings;
		$currentValues = $values + $currentValues;

		return new static( $currentValues );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->strings;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function appendList( RepresentsNativeStringList|RepresentsStringList $list ): static
	{
		return new static( $this->toNativeType() + ($list instanceof RepresentsNativeStringList ? $list->toNativeType() : $list->toStrings() ) );
	}

	public function prependList( RepresentsNativeStringList|RepresentsStringList $list ): static
	{
		return new static( $list instanceof RepresentsNativeStringList ? $list->toNativeType() : $list->toStrings() + $this->toNativeType() );
	}
}
