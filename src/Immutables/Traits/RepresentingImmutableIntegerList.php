<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerList;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerList;

trait RepresentingImmutableIntegerList
{
	use RepresentingIntegerList;

	public function exists( int $index ): bool
	{
		return isset( $this->integerTypes[ $index ] );
	}

	public function get( int $index ): RepresentsInteger
	{
		$this->guardIndexExists( $index );

		return $this->integerTypes[ $index ];
	}

	public function set( int $index, RepresentsInteger $value ): static
	{
		$integerTypes           = $this->integerTypes;
		$integerTypes[ $index ] = $value;

		return new self( $integerTypes );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->integerTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( RepresentsInteger...$types ): static
	{
		$currentTypes = $this->integerTypes;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( RepresentsInteger...$types ): static
	{
		$currentTypes = $this->integerTypes;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsIntegerList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsIntegerList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}
}
