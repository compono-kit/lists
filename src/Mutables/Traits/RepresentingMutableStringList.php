<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringList;
use ComponoKit\Types\Lists\Traits\RepresentingStringList;

trait RepresentingMutableStringList
{
	use RepresentingStringList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): RepresentsString
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

	public function append( RepresentsString...$types ): void
	{
		foreach ( $types as $type )
		{
			$this->stringTypes[] = $type;
		}
	}

	public function prepend( RepresentsString...$types ): void
	{
		$this->stringTypes = $types + $this->stringTypes;
	}

	public function appendList( RepresentsStringList $list ): void
	{
		$this->stringTypes += $list->toNativeType();
	}

	public function prependList( RepresentsStringList $list ): void
	{
		$this->stringTypes = $list->toNativeType() + $this->stringTypes;
	}

	private function exists( int $index ): bool
	{
		return isset( $this->stringTypes[ $index ] );
	}

	private function get( int $index ): RepresentsString
	{
		$this->guardIndexExists( $index );

		return $this->stringTypes[ $index ];
	}

	private function set( int $index, RepresentsString $value ): void
	{
		$this->stringTypes[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->stringTypes[ $index ] );
	}
}
