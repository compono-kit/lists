<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingFloatTypeList;

trait RepresentingMutableFloatTypeList
{
	use RepresentingFloatTypeList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->floatTypes, $index, 1 );
	}

	public function replace( int $index, RepresentsFloat $type ): void
	{
		$this->guardIndexExists( $index );

		$this->floatTypes[ $index ] = $type;
	}

	public function append( RepresentsFloat...$types ): void
	{
		array_push( $this->floatTypes, ...$types );
	}

	public function prepend( RepresentsFloat...$types ): void
	{
		array_unshift( $this->floatTypes, ...$types );
	}

	public function appendList( RepresentsFloatTypeList $list ): void
	{
		array_push( $this->floatTypes, ...$list->toNativeType() );
	}

	public function prependList( RepresentsFloatTypeList $list ): void
	{
		array_unshift( $this->floatTypes, ...$list->toNativeType() );
	}

	public function reverse(): void
	{
		$this->floatTypes = array_reverse( $this->floatTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->floatTypes );
	}

	public function unique(): void
	{
		$existingTypes = [];
		$uniqueTypes   = [];

		foreach ( $this->floatTypes as $type )
		{
			$key = $type->toNativeType();

			if ( !isset( $existingTypes[ $key ] ) )
			{
				$existingTypes[ $key ] = true;
				$uniqueTypes[]         = $type;
			}
		}

		$this->floatTypes = $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): void
	{
		usort(
			$this->floatTypes,
			function ( RepresentsFloat $firstType, RepresentsFloat $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);
	}
}
