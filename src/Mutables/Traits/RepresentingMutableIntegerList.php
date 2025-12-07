<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerList;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerList;

trait RepresentingMutableIntegerList
{
	use RepresentingIntegerList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): RepresentsInteger
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

	public function append( RepresentsInteger...$types ): void
	{
		foreach ( $types as $type )
		{
			$this->integerTypes[] = $type;
		}
	}

	public function prepend( RepresentsInteger...$types ): void
	{
		$this->integerTypes = $types + $this->integerTypes;
	}

	public function appendList( RepresentsIntegerList $list ): void
	{
		$this->integerTypes += $list->toNativeType();
	}

	public function prependList( RepresentsIntegerList $list ): void
	{
		$this->integerTypes = $list->toNativeType() + $this->integerTypes;
	}

	private function exists( int $index ): bool
	{
		return isset( $this->integerTypes[ $index ] );
	}

	private function get( int $index ): RepresentsInteger
	{
		$this->guardIndexExists( $index );

		return $this->integerTypes[ $index ];
	}

	private function set( int $index, RepresentsInteger $value ): void
	{
		$this->integerTypes[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->integerTypes[ $index ] );
	}
}
