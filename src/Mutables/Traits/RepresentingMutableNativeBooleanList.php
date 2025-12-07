<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeBooleanList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeBooleanList;

trait RepresentingMutableNativeBooleanList
{
	use RepresentingNativeBooleanList;

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

	public function appendList( RepresentsNativeBooleanList|RepresentsBooleanList $list ): void
	{
		$this->booleans += $list instanceof RepresentsBooleanList ? $list->toBooleans() : $list->toNativeType();
	}

	public function prependList( RepresentsNativeBooleanList|RepresentsBooleanList $list ): void
	{
		$this->booleans = ($list instanceof RepresentsBooleanList ? $list->toBooleans() : $list->toNativeType()) + $this->booleans;
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
