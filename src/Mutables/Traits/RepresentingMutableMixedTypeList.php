<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingMixedTypeList;

trait RepresentingMutableMixedTypeList
{
	use RepresentingMixedTypeList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->mixedTypes, $index, 1 );
	}

	public function replace( int $index, RepresentsType $type ): void
	{
		$this->guardIndexExists( $index );

		$this->mixedTypes[ $index ] = $type;
	}

	public function append( RepresentsType...$types ): void
	{
		array_push( $this->mixedTypes, ...$types );
	}

	public function prepend( RepresentsType...$types ): void
	{
		array_unshift( $this->mixedTypes, ...$types );
	}

	public function appendList( RepresentsMixedTypeList $list ): void
	{
		array_push( $this->mixedTypes, ...$list->toNativeType() );
	}

	public function prependList( RepresentsMixedTypeList $list ): void
	{
		array_unshift( $this->mixedTypes, ...$list->toNativeType() );
	}

	public function reverse(): void
	{
		$this->mixedTypes = array_reverse( $this->mixedTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->mixedTypes );
	}

	public function unique(): void
	{
		$this->mixedTypes = array_unique( $this->mixedTypes );
	}

	public function sort( ComparesTypes $comparer ): void
	{
		usort(
			$this->mixedTypes,
			function ( RepresentsType $firstType, RepresentsType $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);
	}
}
