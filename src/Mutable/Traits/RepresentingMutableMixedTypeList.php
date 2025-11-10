<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable\Traits;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingMixedTypeList;

trait RepresentingMutableMixedTypeList
{
	use RepresentingMixedTypeList;

	public function append( RepresentsType...$types ): void
	{
		foreach ( $types as $type )
		{
			$this->mixedTypes[] = $type;
		}
	}

	public function prepend( RepresentsType...$types ): void
	{
		$this->mixedTypes = $types + $this->mixedTypes;
	}

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		unset( $this->mixedTypes[ $index ] );
	}

	public function appendList( RepresentsMixedTypeList $list ): void
	{
		$this->mixedTypes += $list->toNativeType();
	}

	public function prependList( RepresentsMixedTypeList $list ): void
	{
		$this->mixedTypes = $list->toNativeType() + $this->mixedTypes;
	}
}
