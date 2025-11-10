<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeMixedTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeMixedTypeList;

trait RepresentingImmutableNativeMixedTypeList
{
	use RepresentingNativeMixedTypeList;

	public function append( int|float|string|bool|array...$values ): static
	{
		$currentValues = $this->nativeMixedTypes;
		foreach ( $values as $value )
		{
			$currentValues[] = $value;
		}

		return new static( $currentValues );
	}

	public function prepend( int|float|string|bool|array...$values ): static
	{
		$currentValues = $this->nativeMixedTypes;
		$currentValues = $values + $currentValues;

		return new static( $currentValues );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->nativeMixedTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function appendList( RepresentsNativeMixedTypeList|RepresentsMixedTypeList $list ): static
	{
		return new static( $this->toNativeType() + ($list instanceof RepresentsNativeMixedTypeList ? $list->toNativeType() : $list->toNativeTypes()) );
	}

	public function prependList( RepresentsNativeMixedTypeList|RepresentsMixedTypeList $list ): static
	{
		return new static( $list instanceof RepresentsNativeMixedTypeList ? $list->toNativeType() : $list->toNativeTypes() + $this->toNativeType() );
	}
}
