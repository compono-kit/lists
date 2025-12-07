<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatList;
use ComponoKit\Types\Lists\Traits\RepresentingFloatList;

trait RepresentingMutableFloatList
{
	use RepresentingFloatList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): RepresentsFloat
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

	public function append( RepresentsFloat...$types ): void
	{
		foreach ( $types as $type )
		{
			$this->floatTypes[] = $type;
		}
	}

	public function prepend( RepresentsFloat...$types ): void
	{
		$this->floatTypes = $types + $this->floatTypes;
	}

	public function appendList( RepresentsFloatList $list ): void
	{
		$this->floatTypes += $list->toNativeType();
	}

	public function prependList( RepresentsFloatList $list ): void
	{
		$this->floatTypes = $list->toNativeType() + $this->floatTypes;
	}

	private function exists( int $index ): bool
	{
		return isset( $this->floatTypes[ $index ] );
	}

	private function get( int $index ): RepresentsFloat
	{
		$this->guardIndexExists( $index );

		return $this->floatTypes[ $index ];
	}

	private function set( int $index, RepresentsFloat $value ): void
	{
		$this->floatTypes[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->floatTypes[ $index ] );
	}
}
