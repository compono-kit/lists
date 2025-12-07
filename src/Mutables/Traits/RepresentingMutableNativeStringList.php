<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsNativeStringList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeStringList;

trait RepresentingMutableNativeStringList
{
	use RepresentingNativeStringList;

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

	public function appendList( RepresentsNativeStringList|RepresentsStringList $list ): void
	{
		$this->strings += $list instanceof RepresentsStringList ? $list->toStrings() : $list->toNativeType();
	}

	public function prependList( RepresentsNativeStringList|RepresentsStringList $list ): void
	{
		$this->strings = ($list instanceof RepresentsStringList ? $list->toStrings() : $list->toNativeType()) + $this->strings;
	}

	private function exists( int $index ): bool
	{
		return isset( $this->strings[ $index ] );
	}

	private function get( int $index ): int|float|string|bool|array
	{
		$this->guardIndexExists( $index );

		return $this->nativeMixedTypes[ $index ];
	}

	private function set( int $index, int|float|string|bool|array $value ): void
	{
		$this->nativeMixedTypes[ $index ] = $value;
	}

	private function remove( int $index ): void
	{
		unset( $this->nativeMixedTypes[ $index ] );
	}
}
