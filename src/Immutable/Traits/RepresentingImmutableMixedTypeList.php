<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable\Traits;

use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingMixedTypeList;

trait RepresentingImmutableMixedTypeList
{
	use RepresentingMixedTypeList;

	public function append( int|float|string|bool|array...$types ): static
	{
		$currentTypes = $this->mixedTypes;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( int|float|string|bool|array...$types ): static
	{
		$currentTypes = $this->mixedTypes;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->mixedTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function appendList( RepresentsMixedTypeList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsMixedTypeList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}
}
