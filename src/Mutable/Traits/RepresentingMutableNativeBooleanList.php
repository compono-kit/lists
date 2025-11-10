<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanList;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeBooleanList;
use ComponoKit\Types\Lists\Traits\RepresentingNativeBooleanList;

trait RepresentingMutableNativeBooleanList
{
	use RepresentingNativeBooleanList;

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

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		unset( $this->booleans[ $index ] );
	}

	public function appendList( RepresentsNativeBooleanList|RepresentsBooleanList $list ): void
	{
		$this->booleans += $list instanceof RepresentsBooleanList ? $list->toBooleans() : $list->toNativeType();
	}

	public function prependList( RepresentsNativeBooleanList|RepresentsBooleanList $list ): void
	{
		$this->booleans = ($list instanceof RepresentsBooleanList ? $list->toBooleans() : $list->toNativeType()) + $this->booleans;
	}
}
