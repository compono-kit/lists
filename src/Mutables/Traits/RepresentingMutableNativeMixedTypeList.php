<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeMixedTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeMixedTypeList;

trait RepresentingMutableNativeMixedTypeList
{
	use RepresentingNativeMixedTypeList;

	public function offsetExists( $offset ): bool
	{
		return $this->exists( $offset );
	}

	public function offsetGet( $offset ): int|float|string|bool|array
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

	public function append( int|float|string|bool|array...$values ): void
	{
		foreach ( $values as $value )
		{
			$this->nativeMixedTypes[] = $value;
		}
	}

	public function prepend( int|float|string|bool|array...$values ): void
	{
		$this->nativeMixedTypes = $values + $this->nativeMixedTypes;
	}

	public function appendList( RepresentsNativeMixedTypeList|RepresentsMixedTypeList $list ): void
	{
		$this->nativeMixedTypes += $list instanceof RepresentsMixedTypeList ? $list->toNativeTypes() : $list->toNativeType();
	}

	public function prependList( RepresentsNativeMixedTypeList|RepresentsMixedTypeList $list ): void
	{
		$this->nativeMixedTypes = ($list instanceof RepresentsMixedTypeList ? $list->toNativeTypes() : $list->toNativeType()) + $this->nativeMixedTypes;
	}

	private function exists( int $index ): bool
	{
		return isset( $this->nativeMixedTypes[ $index ] );
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
