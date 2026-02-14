<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanTypeList;

trait RepresentingMutableBooleanTypeList
{
	use RepresentingBooleanTypeList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): RepresentsBoolean
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

	public function append( RepresentsBoolean...$types ): void
	{
		foreach ( $types as $type )
		{
			$this->booleanTypes[] = $type;
		}
	}

	public function prepend( RepresentsBoolean...$types ): void
	{
		$this->booleanTypes = $types + $this->booleanTypes;
	}

	public function appendList( RepresentsBooleanTypeList $list ): void
	{
		$this->booleanTypes += $list->toNativeType();
	}

	public function prependList( RepresentsBooleanTypeList $list ): void
	{
		$this->booleanTypes = $list->toNativeType() + $this->booleanTypes;
	}

	public function reverse(): void
	{
		$this->booleanTypes = array_reverse( $this->booleanTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->booleanTypes );
	}

	public function replace( int $index, RepresentsBoolean $type ): void
	{
		$this->booleanTypes[ $index ] = $type;
	}

	public function unique(): void
	{
		$this->booleanTypes = array_unique( $this->booleanTypes );
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

	private function exists( int $index ): bool
	{
		return isset( $this->booleanTypes[ $index ] );
	}

	private function get( int $index ): RepresentsBoolean
	{
		$this->guardIndexExists( $index );

		return $this->booleanTypes[ $index ];
	}

	private function set( int $index, RepresentsBoolean $value ): void
	{
		$this->booleanTypes[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->booleanTypes[ $index ] );
	}
}
