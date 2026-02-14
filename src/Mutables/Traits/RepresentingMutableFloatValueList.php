<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatValueList;
use ComponoKit\Types\Lists\Traits\RepresentingFloatValueList;

trait RepresentingMutableFloatValueList
{
	use RepresentingFloatValueList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): float
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

	public function append( float...$values ): void
	{
		foreach ( $values as $value )
		{
			$this->floats[] = $value;
		}
	}

	public function prepend( float...$values ): void
	{
		$this->floats = $values + $this->floats;
	}

	public function appendList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): void
	{
		$this->floats += $list instanceof RepresentsFloatTypeList ? $list->toFloats() : $list->toNativeType();
	}

	public function prependList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): void
	{
		$this->floats = ($list instanceof RepresentsFloatTypeList ? $list->toFloats() : $list->toNativeType()) + $this->floats;
	}

	public function reverse(): void
	{
		$this->floats = array_reverse( $this->floats );
	}

	public function shuffle(): void
	{
		shuffle( $this->floats );
	}

	public function replace( int $index, float $value ): void
	{
		$this->floats[ $index ] = $value;
	}

	public function unique(): void
	{
		$this->floats = array_unique( $this->floats );
	}

	public function sort( ComparesValues $comparer ): void
	{
		usort(
			$this->floats,
			function ( float $firstValue, float $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);
	}

	private function exists( int $index ): bool
	{
		return isset( $this->floats[ $index ] );
	}

	private function get( int $index ): float
	{
		$this->guardIndexExists( $index );

		return $this->floats[ $index ];
	}

	private function set( int $index, float $value ): void
	{
		$this->floats[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->floats[ $index ] );
	}
}
