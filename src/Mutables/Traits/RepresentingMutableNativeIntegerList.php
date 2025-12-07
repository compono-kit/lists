<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeIntegerList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeIntegerList;

trait RepresentingMutableNativeIntegerList
{
	use RepresentingNativeIntegerList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): int
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

	public function append( int...$values ): void
	{
		foreach ( $values as $value )
		{
			$this->integers[] = $value;
		}
	}

	public function prepend( int...$values ): void
	{
		$this->integers = $values + $this->integers;
	}

	public function appendList( RepresentsNativeIntegerList|RepresentsIntegerList $list ): void
	{
		$this->integers += $list instanceof RepresentsIntegerList ? $list->toIntegers() : $list->toNativeType();
	}

	public function prependList( RepresentsNativeIntegerList|RepresentsIntegerList $list ): void
	{
		$this->integers = ($list instanceof RepresentsIntegerList ? $list->toIntegers() : $list->toNativeType()) + $this->integers;
	}

	private function exists( int $index ): bool
	{
		return isset( $this->integers[ $index ] );
	}

	private function get( int $index ): int
	{
		$this->guardIndexExists( $index );

		return $this->integers[ $index ];
	}

	private function set( int $index, int $value ): void
	{
		$this->integers[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->integers[ $index ] );
	}
}
