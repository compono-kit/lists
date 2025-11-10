<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsNativeStringList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeStringList;

trait RepresentingMutableNativeStringList
{
	use RepresentingNativeStringList;

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

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		unset( $this->strings[ $index ] );
	}

	public function appendList( RepresentsNativeStringList|RepresentsStringList $list ): void
	{
		$this->strings += $list instanceof RepresentsStringList ? $list->toStrings() : $list->toNativeType();
	}

	public function prependList( RepresentsNativeStringList|RepresentsStringList $list ): void
	{
		$this->strings = ($list instanceof RepresentsStringList ? $list->toStrings() : $list->toNativeType()) + $this->strings;
	}
}
