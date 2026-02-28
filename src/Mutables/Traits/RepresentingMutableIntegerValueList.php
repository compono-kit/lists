<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerValueList;

trait RepresentingMutableIntegerValueList
{
	use RepresentingIntegerValueList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->integers, $index, 1 );
	}

	public function replace( int $index, int $value ): void
	{
		$this->guardIndexExists( $index );

		$this->integers[ $index ] = $value;
	}

	public function append( int...$values ): void
	{
		array_push( $this->integers, ...$values );
	}

	public function prepend( int...$values ): void
	{
		array_unshift( $this->integers, ...$values );
	}

	public function appendList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): void
	{
		array_push( $this->integers, ...($list instanceof RepresentsIntegerTypeList ? $list->toIntegers() : $list->toNativeType()) );
	}

	public function prependList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): void
	{
		array_unshift( $this->integers, ...($list instanceof RepresentsIntegerTypeList ? $list->toIntegers() : $list->toNativeType()) );
	}

	public function reverse(): void
	{
		$this->integers = array_reverse( $this->integers );
	}

	public function shuffle(): void
	{
		shuffle( $this->integers );
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
}
