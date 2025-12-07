<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables\Traits;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringList;
use ComponoKit\Types\Lists\Traits\RepresentingStringList;

trait RepresentingImmutableStringList
{
	use RepresentingStringList;

	public function exists( int $index ): bool
	{
		return isset( $this->stringTypes[ $index ] );
	}

	public function set( int $index, RepresentsString $value ): static
	{
		$stringTypes           = $this->stringTypes;
		$stringTypes[ $index ] = $value;

		return new self( $stringTypes );
	}

	public function get( int $index ): RepresentsString
	{
		$this->guardIndexExists( $index );

		return $this->stringTypes[ $index ];
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->stringTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

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

	public function appendList( RepresentsStringList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsStringList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}
}
