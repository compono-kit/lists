<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanTypeList;

trait RepresentingMutableBooleanTypeList
{
	use RepresentingBooleanTypeList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->booleanTypes, $index, 1 );
	}

	public function replace( int $index, RepresentsBoolean $type ): void
	{
		$this->guardIndexExists( $index );

		$this->booleanTypes[ $index ] = $type;
	}

	public function append( RepresentsBoolean...$types ): void
	{
		array_push( $this->booleanTypes, ...$types );
	}

	public function prepend( RepresentsBoolean...$types ): void
	{
		array_unshift( $this->booleanTypes, ...$types );
	}

	public function appendList( RepresentsBooleanTypeList $list ): void
	{
		array_push( $this->booleanTypes, ...$list->toNativeType() );
	}

	public function prependList( RepresentsBooleanTypeList $list ): void
	{
		array_unshift( $this->booleanTypes, ...$list->toNativeType() );
	}

	public function reverse(): void
	{
		$this->booleanTypes = array_reverse( $this->booleanTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->booleanTypes );
	}

	public function unique(): void
	{
		$existingTypes = [];
		$uniqueTypes   = [];

		foreach ( $this->booleanTypes as $type )
		{
			$key = $type->toNativeType();

			if ( !isset( $existingTypes[ $key ] ) )
			{
				$existingTypes[ $key ] = true;
				$uniqueTypes[]         = $type;
			}
		}

		$this->booleanTypes = $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): void
	{
		usort(
			$this->booleanTypes,
			function ( RepresentsBoolean $firstType, RepresentsBoolean $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);
	}
}
