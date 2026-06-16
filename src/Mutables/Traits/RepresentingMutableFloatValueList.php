<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatValueList;
use ComponoKit\Types\Lists\Traits\RepresentingFloatValueList;

trait RepresentingMutableFloatValueList
{
	use RepresentingFloatValueList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->floats, $index, 1 );
	}

	public function replace( int $index, float $value ): void
	{
		$this->guardIndexExists( $index );

		$this->floats[ $index ] = $value;
	}

	public function append( float...$values ): void
	{
		array_push( $this->floats, ...$values );
	}

	public function prepend( float...$values ): void
	{
		array_unshift( $this->floats, ...$values );
	}

	public function appendList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): void
	{
		array_push( $this->floats, ...($list instanceof RepresentsFloatTypeList ? $list->toFloats() : $list->toNativeType()) );
	}

	public function prependList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): void
	{
		array_unshift( $this->floats, ...($list instanceof RepresentsFloatTypeList ? $list->toFloats() : $list->toNativeType()) );
	}

	public function reverse(): void
	{
		$this->floats = array_reverse( $this->floats );
	}

	public function shuffle(): void
	{
		shuffle( $this->floats );
	}

	public function unique(): void
	{
		$this->floats = array_values( array_unique( $this->floats ) );
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
}
