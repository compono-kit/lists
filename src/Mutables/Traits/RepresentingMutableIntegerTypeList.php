<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerTypeList;

trait RepresentingMutableIntegerTypeList
{
	use RepresentingIntegerTypeList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->integerTypes, $index, 1 );
	}

	public function replace( int $index, RepresentsInteger $type ): void
	{
		$this->guardIndexExists( $index );

		$this->integerTypes[ $index ] = $type;
	}

	public function append( RepresentsInteger...$types ): void
	{
		array_push( $this->integerTypes, ...$types );
	}

	public function prepend( RepresentsInteger...$types ): void
	{
		array_unshift( $this->integerTypes, ...$types );
	}

	public function appendList( RepresentsIntegerTypeList $list ): void
	{
		array_push( $this->integerTypes, ...$list->toNativeType() );
	}

	public function prependList( RepresentsIntegerTypeList $list ): void
	{
		array_unshift( $this->integerTypes, ...$list->toNativeType() );
	}

	public function reverse(): void
	{
		$this->integerTypes = array_reverse( $this->integerTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->integerTypes );
	}

	public function unique(): void
	{
		$existingTypes = [];
		$uniqueTypes   = [];

		foreach ( $this->integerTypes as $type )
		{
			$key = $type->toNativeType();

			if ( !isset( $existingTypes[ $key ] ) )
			{
				$existingTypes[ $key ] = true;
				$uniqueTypes[]         = $type;
			}
		}

		$this->integerTypes = $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): void
	{
		usort(
			$this->integerTypes,
			function ( RepresentsInteger $firstType, RepresentsInteger $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);
	}
}
