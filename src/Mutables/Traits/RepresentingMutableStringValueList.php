<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingStringValueList;

trait RepresentingMutableStringValueList
{
	use RepresentingStringValueList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): string
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

	public function append( string...$values ): void
	{
		foreach ( $values as $value )
		{
			$this->strings[] = $value;
		}
	}

	public function prepend( string...$values ): void
	{
		$this->strings = $values + $this->strings;
	}

	public function appendList( RepresentsStringValueList|RepresentsStringTypeList $list ): void
	{
		$this->strings += $list instanceof RepresentsStringTypeList ? $list->toStrings() : $list->toNativeType();
	}

	public function prependList( RepresentsStringValueList|RepresentsStringTypeList $list ): void
	{
		$this->strings = ($list instanceof RepresentsStringTypeList ? $list->toStrings() : $list->toNativeType()) + $this->strings;
	}

	public function reverse(): void
	{
		$this->strings = array_reverse( $this->strings );
	}

	public function shuffle(): void
	{
		shuffle( $this->strings );
	}

	public function replace( int $index, string $value ): void
	{
		$this->strings[ $index ] = $value;
	}

	public function unique(): void
	{
		$this->strings = array_unique( $this->strings );
	}

	public function sort( ComparesValues $comparer ): void
	{
		usort(
			$this->strings,
			function ( string $firstValue, string $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);
	}

	private function exists( int $index ): bool
	{
		return isset( $this->strings[ $index ] );
	}

	private function get( int $index ): string
	{
		$this->guardIndexExists( $index );

		return $this->strings[ $index ];
	}

	private function set( int $index, string $value ): void
	{
		$this->strings[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->strings[ $index ] );
	}
}
