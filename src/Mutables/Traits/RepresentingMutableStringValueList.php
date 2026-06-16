<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringValueList;
use ComponoKit\Types\Lists\Traits\RepresentingStringValueList;

trait RepresentingMutableStringValueList
{
	use RepresentingStringValueList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->strings, $index, 1 );
	}

	public function replace( int $index, string $value ): void
	{
		$this->guardIndexExists( $index );

		$this->strings[ $index ] = $value;
	}

	public function append( string...$values ): void
	{
		array_push( $this->strings, ...$values );
	}

	public function prepend( string...$values ): void
	{
		array_unshift( $this->strings, ...$values );
	}

	public function appendList( RepresentsStringValueList|RepresentsStringTypeList $list ): void
	{
		array_push( $this->strings, ...($list instanceof RepresentsStringTypeList ? $list->toStrings() : $list->toNativeType()) );
	}

	public function prependList( RepresentsStringValueList|RepresentsStringTypeList $list ): void
	{
		array_unshift( $this->strings, ...($list instanceof RepresentsStringTypeList ? $list->toStrings() : $list->toNativeType()) );
	}

	public function reverse(): void
	{
		$this->strings = array_reverse( $this->strings );
	}

	public function shuffle(): void
	{
		shuffle( $this->strings );
	}

	public function unique(): void
	{
		$this->strings = array_values( array_unique( $this->strings ) );
	}

	public function sort( ComparesValues $comparer ): void
	{
		usort(
			$this->strings,
			function ( string $firstValue, string $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);
	}
}
