<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable\Traits;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerList;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerList;

trait RepresentingMutableIntegerList
{
	use RepresentingIntegerList;

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

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		unset( $this->integerTypes[ $index ] );
	}

	public function appendList( RepresentsIntegerList $list ): void
	{
		$this->integerTypes += $list->toNativeType();
	}

	public function prependList( RepresentsIntegerList $list ): void
	{
		$this->integerTypes = $list->toNativeType() + $this->integerTypes;
	}
}
