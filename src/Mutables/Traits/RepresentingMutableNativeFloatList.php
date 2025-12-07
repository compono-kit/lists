<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsFloatList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeFloatList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeFloatList;

trait RepresentingMutableNativeFloatList
{
	use RepresentingNativeFloatList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): float
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

	public function append( float...$values ): void
	{
		foreach ( $values as $value )
		{
			$this->floats[] = $value;
		}
	}

	public function prepend( float...$values ): void
	{
		$this->floats = $values + $this->floats;
	}

	public function appendList( RepresentsNativeFloatList|RepresentsFloatList $list ): void
	{
		$this->floats += $list instanceof RepresentsFloatList ? $list->toFloats() : $list->toNativeType();
	}

	public function prependList( RepresentsNativeFloatList|RepresentsFloatList $list ): void
	{
		$this->floats = ($list instanceof RepresentsFloatList ? $list->toFloats() : $list->toNativeType()) + $this->floats;
	}

	private function exists( int $index ): bool
	{
		return isset( $this->floats[ $index ] );
	}

	private function get( int $index ): float
	{
		$this->guardIndexExists( $index );

		return $this->floats[ $index ];
	}

	private function set( int $index, float $value ): void
	{
		$this->floats[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->floats[ $index ] );
	}
}
