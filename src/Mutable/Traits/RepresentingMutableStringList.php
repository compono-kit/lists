<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable\Traits;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringList;
use ComponoKit\Types\Lists\Traits\RepresentingStringList;

trait RepresentingMutableStringList
{
	use RepresentingStringList;

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

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		unset( $this->stringTypes[ $index ] );
	}

	public function appendList( RepresentsStringList $list ): void
	{
		$this->stringTypes += $list->toNativeType();
	}

	public function prependList( RepresentsStringList $list ): void
	{
		$this->stringTypes = $list->toNativeType() + $this->stringTypes;
	}
}
