<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatList;
use ComponoKit\Types\Lists\Traits\RepresentingFloatList;

trait RepresentingImmutableFloatList
{
	use RepresentingFloatList;

	public function exists( int $index ): bool
	{
		return isset( $this->floatTypes[ $index ] );
	}

	public function get( int $index ): RepresentsFloat
	{
		$this->guardIndexExists( $index );

		return $this->floatTypes[ $index ];
	}

	public function set( int $index, RepresentsFloat $value ): static
	{
		$floatTypes           = $this->floatTypes;
		$floatTypes[ $index ] = $value;

		return new self( $floatTypes );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->floatTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( RepresentsFloat...$types ): static
	{
		$currentTypes = $this->floatTypes;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( RepresentsFloat...$types ): static
	{
		$currentTypes = $this->floatTypes;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsFloatList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsFloatList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}
}
