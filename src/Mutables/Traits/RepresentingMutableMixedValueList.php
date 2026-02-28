<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedValueList;
use ComponoKit\Types\Lists\Traits\RepresentingMixedValueList;

trait RepresentingMutableMixedValueList
{
	use RepresentingMixedValueList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->mixedValues, $index, 1 );
	}

	public function replace( int $index, int|float|string|bool|array $value ): void
	{
		$this->guardIndexExists( $index );

		$this->mixedValues[ $index ] = $value;
	}

	public function append( int|float|string|bool|array...$values ): void
	{
		array_push( $this->mixedValues, ...$values );
	}

	public function prepend( int|float|string|bool|array...$values ): void
	{
		array_unshift( $this->mixedValues, ...$values );
	}

	public function appendList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): void
	{
		array_push( $this->mixedValues, ...($list instanceof RepresentsMixedTypeList ? $list->toNativeTypes() : $list->toNativeType()) );
	}

	public function prependList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): void
	{
		array_unshift( $this->mixedValues, ...($list instanceof RepresentsMixedTypeList ? $list->toNativeTypes() : $list->toNativeType()) );
	}

	public function reverse(): void
	{
		$this->mixedValues = array_reverse( $this->mixedValues );
	}

	public function shuffle(): void
	{
		shuffle( $this->mixedValues );
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
}
