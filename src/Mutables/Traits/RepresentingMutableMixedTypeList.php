<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingMixedTypeList;

trait RepresentingMutableMixedTypeList
{
	use RepresentingMixedTypeList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): RepresentsType
	{
		return $this->get( $offset );
	}

	public function offsetSet( $offset, $value ): void
	{
		$this->set( $offset, $value );
	}

	public function offsetUnset( $offset ): void
	{
		$this->remove( $offset );
	}

	public function append( RepresentsType...$types ): void
	{
		foreach ( $types as $type )
		{
			$this->mixedTypes[] = $type;
		}
	}

	public function prepend( RepresentsType...$types ): void
	{
		$this->mixedTypes = $types + $this->mixedTypes;
	}

	public function appendList( RepresentsMixedTypeList $list ): void
	{
		$this->mixedTypes += $list->toNativeType();
	}

	public function prependList( RepresentsMixedTypeList $list ): void
	{
		$this->mixedTypes = $list->toNativeType() + $this->mixedTypes;
	}

	public function reverse(): void
	{
		$this->mixedTypes = array_reverse( $this->mixedTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->mixedTypes );
	}

	public function replace( int $index, RepresentsType $type ): void
	{
		$this->mixedTypes[ $index ] = $type;
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

	private function exists( int $index ): bool
	{
		return isset( $this->mixedTypes[ $index ] );
	}

	private function get( int $index ): RepresentsType
	{
		$this->guardIndexExists( $index );

		return $this->mixedTypes[ $index ];
	}

	private function set( int $index, RepresentsType $value ): void
	{
		$this->mixedTypes[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->mixedTypes[ $index ] );
	}
}
