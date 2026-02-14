<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerValueList;

trait RepresentingMutableIntegerValueList
{
	use RepresentingIntegerValueList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): int
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

	public function append( int...$values ): void
	{
		foreach ( $values as $value )
		{
			$this->integers[] = $value;
		}
	}

	public function prepend( int...$values ): void
	{
		$this->integers = $values + $this->integers;
	}

	public function appendList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): void
	{
		$this->integers += $list instanceof RepresentsIntegerTypeList ? $list->toIntegers() : $list->toNativeType();
	}

	public function prependList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): void
	{
		$this->integers = ($list instanceof RepresentsIntegerTypeList ? $list->toIntegers() : $list->toNativeType()) + $this->integers;
	}

	public function reverse(): void
	{
		$this->integers = array_reverse( $this->integers );
	}

	public function shuffle(): void
	{
		shuffle( $this->integers );
	}

	public function replace( int $index, int $value ): void
	{
		$this->integers[ $index ] = $value;
	}

	public function unique(): void
	{
		$this->integers = array_unique( $this->integers );
	}

	public function sort( ComparesValues $comparer ): void
	{
		usort(
			$this->integers,
			function ( int $firstValue, int $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);
	}

	private function exists( int $index ): bool
	{
		return isset( $this->integers[ $index ] );
	}

	private function get( int $index ): int
	{
		$this->guardIndexExists( $index );

		return $this->integers[ $index ];
	}

	private function set( int $index, int $value ): void
	{
		$this->integers[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->integers[ $index ] );
	}
}
