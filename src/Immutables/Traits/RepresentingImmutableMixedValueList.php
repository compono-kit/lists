<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedValueList;
use ComponoKit\Types\Lists\Traits\RepresentingMixedValueList;

trait RepresentingImmutableMixedValueList
{
	use RepresentingMixedValueList;

	public function exists( int $index ): bool
	{
		return isset( $this->mixedValues[ $index ] );
	}

	public function get( int $index ): int|float|string|bool|array
	{
		$this->guardIndexExists( $index );

		return $this->mixedValues[ $index ];
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->mixedValues;
		array_splice( $values, $index, 1 );

		return new static( ...$values );
	}

	public function append( int|float|string|bool|array...$values ): static
	{
		return new static( ...$this->mixedValues, ...$values );
	}

	public function prepend( int|float|string|bool|array...$values ): static
	{
		return new static( ...$values, ...$this->mixedValues );
	}

	public function appendList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...($list instanceof RepresentsMixedValueList ? $list->toNativeType() : $list->toNativeTypes()) );
	}

	public function prependList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): static
	{
		return new static( ...($list instanceof RepresentsMixedValueList ? $list->toNativeType() : $list->toNativeTypes()), ...$this->toNativeType() );
	}
}
