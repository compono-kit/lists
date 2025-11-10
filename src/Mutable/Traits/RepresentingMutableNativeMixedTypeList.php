<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeMixedTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeMixedTypeList;

trait RepresentingMutableNativeMixedTypeList
{
	use RepresentingNativeMixedTypeList;

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

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		unset( $this->nativeMixedTypes[ $index ] );
	}

	public function appendList( RepresentsNativeMixedTypeList|RepresentsMixedTypeList $list ): void
	{
		$this->nativeMixedTypes += $list instanceof RepresentsMixedTypeList ? $list->toNativeTypes() : $list->toNativeType();
	}

	public function prependList( RepresentsNativeMixedTypeList|RepresentsMixedTypeList $list ): void
	{
		$this->nativeMixedTypes = ($list instanceof RepresentsMixedTypeList ? $list->toNativeTypes() : $list->toNativeType()) + $this->nativeMixedTypes;
	}
}
