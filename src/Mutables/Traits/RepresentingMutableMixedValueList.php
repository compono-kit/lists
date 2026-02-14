<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedValueList;
use ComponoKit\Types\Lists\Traits\RepresentingMixedValueList;

trait RepresentingMutableMixedValueList
{
	use RepresentingMixedValueList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): int|float|string|bool|array
	{
		return $this->get( $offset );
	}

	public function offsetSet( $offset, $value ): void
	{
		$this->set( $offset, $value );
	}

	public function offsetUnset( $offset ): void
	{
		$this->remove( $offset );
	}

	public function append( int|float|string|bool|array...$values ): void
	{
		foreach ( $values as $value )
		{
			$this->mixedValues[] = $value;
		}
	}

	public function prepend( int|float|string|bool|array...$values ): void
	{
		$this->mixedValues = $values + $this->mixedValues;
	}

	public function appendList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): void
	{
		$this->mixedValues += $list instanceof RepresentsMixedTypeList ? $list->toNativeTypes() : $list->toNativeType();
	}

	public function prependList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): void
	{
		$this->mixedValues = ($list instanceof RepresentsMixedTypeList ? $list->toNativeTypes() : $list->toNativeType()) + $this->mixedValues;
	}

	public function reverse(): void
	{
		$this->mixedValues = array_reverse( $this->mixedValues );
	}

	public function shuffle(): void
	{
		shuffle( $this->mixedValues );
	}

	public function replace( int $index, int|float|string|bool|array $value ): void
	{
		$this->mixedValues[ $index ] = $value;
	}

	public function unique(): void
	{
		$this->mixedValues = array_unique( $this->mixedValues );
	}

	public function sort( ComparesValues $comparer ): void
	{
		usort(
			$this->mixedValues,
			function ( int|float|string|bool|array $firstValue, int|float|string|bool|array $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);
	}

	private function exists( int $index ): bool
	{
		return isset( $this->mixedValues[ $index ] );
	}

	private function get( int $index ): int|float|string|bool|array
	{
		$this->guardIndexExists( $index );

		return $this->mixedValues[ $index ];
	}

	private function set( int $index, int|float|string|bool|array $value ): void
	{
		$this->mixedValues[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->mixedValues[ $index ] );
	}
}
