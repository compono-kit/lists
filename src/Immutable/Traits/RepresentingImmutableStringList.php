<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable\Traits;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringList;
use ComponoKit\Types\Lists\Traits\RepresentingStringList;

trait RepresentingImmutableStringList
{
	use RepresentingStringList;

	public function append( RepresentsString...$types ): static
	{
		$currentTypes = $this->stringTypes;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( RepresentsString...$types ): static
	{
		$currentTypes = $this->stringTypes;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->stringTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function appendList( RepresentsStringList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsStringList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}
}
