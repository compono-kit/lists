<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingStringTypeList;

trait RepresentingMutableStringTypeList
{
	use RepresentingStringTypeList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->stringTypes, $index, 1 );
	}

	public function replace( int $index, RepresentsString $type ): void
	{
		$this->guardIndexExists( $index );

		$this->stringTypes[ $index ] = $type;
	}

	public function append( RepresentsString...$types ): void
	{
		array_push( $this->stringTypes, ...$types );
	}

	public function prepend( RepresentsString...$types ): void
	{
		array_unshift( $this->stringTypes, ...$types );
	}

	public function appendList( RepresentsStringTypeList $list ): void
	{
		array_push( $this->stringTypes, ...$list->toNativeType() );
	}

	public function prependList( RepresentsStringTypeList $list ): void
	{
		array_unshift( $this->stringTypes, ...$list->toNativeType() );
	}

	public function reverse(): void
	{
		$this->stringTypes = array_reverse( $this->stringTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->stringTypes );
	}

	public function unique(): void
	{
		$existingTypes = [];
		$uniqueTypes   = [];

		foreach ( $this->stringTypes as $type )
		{
			$key = $type->toNativeType();

			if ( !isset( $existingTypes[ $key ] ) )
			{
				$existingTypes[ $key ] = true;
				$uniqueTypes[]         = $type;
			}
		}

		$this->stringTypes = $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): void
	{
		usort(
			$this->stringTypes,
			function ( RepresentsString $firstType, RepresentsString $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);
	}
}
