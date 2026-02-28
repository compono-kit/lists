<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsStringTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringValueList;
use ComponoKit\Types\Lists\Traits\RepresentingStringValueList;

trait RepresentingImmutableStringValueList
{
	use RepresentingStringValueList;

	public function exists( int $index ): bool
	{
		return isset( $this->strings[ $index ] );
	}

	public function get( int $index ): string
	{
		$this->guardIndexExists( $index );

		return $this->strings[ $index ];
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->strings;
		array_splice( $values, $index, 1 );

		return new static( ...$values );
	}

	public function append( string...$values ): static
	{
		return new static( ...$this->strings, ...$values );
	}

	public function prepend( string...$values ): static
	{
		return new static( ...$values, ...$this->strings );
	}

	public function appendList( RepresentsStringValueList|RepresentsStringTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...($list instanceof RepresentsStringValueList ? $list->toNativeType() : $list->toStrings()) );
	}

	public function prependList( RepresentsStringValueList|RepresentsStringTypeList $list ): static
	{
		return new static( ...($list instanceof RepresentsStringValueList ? $list->toNativeType() : $list->toStrings()), ...$this->toNativeType() );
	}
}
