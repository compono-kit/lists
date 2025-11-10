<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable\Traits;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanList;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanList;

trait RepresentingImmutableBooleanList
{
	use RepresentingBooleanList;

	public function append( RepresentsBoolean...$types ): static
	{
		$currentTypes = $this->booleanTypes;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( RepresentsBoolean...$types ): static
	{
		$currentTypes = $this->booleanTypes;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->booleanTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function appendList( RepresentsBooleanList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsBooleanList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}
}
