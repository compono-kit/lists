<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanValueList;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanValueList;

trait RepresentingMutableBooleanValueList
{
	use RepresentingBooleanValueList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->booleans, $index, 1 );
	}

	public function replace( int $index, bool $value ): void
	{
		$this->guardIndexExists( $index );

		$this->booleans[ $index ] = $value;
	}

	public function append( bool...$values ): void
	{
		array_push( $this->booleans, ...$values );
	}

	public function prepend( bool...$values ): void
	{
		array_unshift( $this->booleans, ...$values );
	}

	public function appendList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): void
	{
		array_push( $this->booleans, ...($list instanceof RepresentsBooleanTypeList ? $list->toBooleans() : $list->toNativeType()) );
	}

	public function prependList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): void
	{
		array_unshift( $this->booleans, ...($list instanceof RepresentsBooleanTypeList ? $list->toBooleans() : $list->toNativeType()) );
	}

	public function reverse(): void
	{
		$this->booleans = array_reverse( $this->booleans );
	}

	public function shuffle(): void
	{
		shuffle( $this->booleans );
	}

	public function unique(): void
	{
		$this->booleans = array_values( array_unique( $this->booleans ) );
	}

	public function sort( ComparesValues $comparer ): void
	{
		usort(
			$this->booleans,
			function ( bool $firstValue, bool $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);
	}
}
