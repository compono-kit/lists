<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable\Traits;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanList;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanList;

trait RepresentingMutableBooleanList
{
	use RepresentingBooleanList;

	public function append( RepresentsBoolean...$types ): void
	{
		foreach ( $types as $type )
		{
			$this->booleanTypes[] = $type;
		}
	}

	public function prepend( RepresentsBoolean...$types ): void
	{
		$this->booleanTypes = $types + $this->booleanTypes;
	}

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		unset( $this->booleanTypes[ $index ] );
	}

	public function appendList( RepresentsBooleanList $list ): void
	{
		$this->booleanTypes += $list->toNativeType();
	}

	public function prependList( RepresentsBooleanList $list ): void
	{
		$this->booleanTypes = $list->toNativeType() + $this->booleanTypes;
	}
}
