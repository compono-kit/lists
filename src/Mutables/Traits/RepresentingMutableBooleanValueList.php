<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanValueList;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanValueList;

trait RepresentingMutableBooleanValueList
{
	use RepresentingBooleanValueList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): bool
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

	public function append( bool...$values ): void
	{
		foreach ( $values as $value )
		{
			$this->booleans[] = $value;
		}
	}

	public function prepend( bool...$values ): void
	{
		$this->booleans = $values + $this->booleans;
	}

	public function appendList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): void
	{
		$this->booleans += $list instanceof RepresentsBooleanTypeList ? $list->toBooleans() : $list->toNativeType();
	}

	public function prependList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): void
	{
		$this->booleans = ($list instanceof RepresentsBooleanTypeList ? $list->toBooleans() : $list->toNativeType()) + $this->booleans;
	}

	public function reverse(): void
	{
		$this->booleans = array_reverse( $this->booleans );
	}

	public function shuffle(): void
	{
		shuffle( $this->booleans );
	}

	public function replace( int $index, bool $value ): void
	{
		$this->booleans[ $index ] = $value;
	}

	public function unique(): void
	{
		$this->booleans = array_unique( $this->booleans );
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

	private function exists( int $index ): bool
	{
		return isset( $this->booleans[ $index ] );
	}

	private function get( int $index ): bool
	{
		$this->guardIndexExists( $index );

		return $this->booleans[ $index ];
	}

	private function set( int $index, bool $value ): void
	{
		$this->booleans[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->booleans[ $index ] );
	}
}
