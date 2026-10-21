<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\Factories\BuildsTypes;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\InvalidMixedTypeListException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableMixedTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedValueList;
use ComponoKit\Types\Lists\Traits\RepresentingMixedTypeList;

abstract class AbstractMixedTypeList implements RepresentsImmutableMixedTypeList
{
	use RepresentingMixedTypeList;

	public function __construct( RepresentsType ...$mixedTypes )
	{
		$this->validate( ...$mixedTypes );

		$this->mixedTypes = $mixedTypes;
	}

	abstract protected static function isValueValid( RepresentsType $type ): bool;

	public static function fromValueList( RepresentsMixedValueList $list, BuildsTypes $factory ): static
	{
		$types = [];
		foreach ( $list as $value )
		{
			$types[] = $factory->build( $value );
		}

		return new static( ...$types );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$types = $this->mixedTypes;
		array_splice( $types, $index, 1 );

		return new static( ...$types );
	}

	public function replace( int $index, RepresentsType $type ): static
	{
		$this->guardIndexExists( $index );

		$types           = $this->mixedTypes;
		$types[ $index ] = $type;

		return new static( ...$types );
	}

	public function append( RepresentsType ...$types ): static
	{
		return new static( ...$this->mixedTypes, ...$types );
	}

	public function prepend( RepresentsType ...$types ): static
	{
		return new static( ...$types, ...$this->mixedTypes );
	}

	public function appendList( RepresentsMixedTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...$list->toNativeType() );
	}

	public function prependList( RepresentsMixedTypeList $list ): static
	{
		return new static( ...$list->toNativeType(), ...$this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( ...array_reverse( $this->mixedTypes ) );
	}

	public function shuffle(): static
	{
		$types = $this->mixedTypes;
		shuffle( $types );

		return new static( ...$types );
	}

	public function unique(): static
	{
		$uniqueTypes = new static();

		foreach ( $this->mixedTypes as $type )
		{
			if ( !$this->existsIn( $type, $uniqueTypes ) )
			{
				$uniqueTypes = $uniqueTypes->append( $type );
			}
		}

		return $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): static
	{
		$types = $this->mixedTypes;
		usort( $types, [ $comparer, 'compare' ] );

		return new static( ...$types );
	}

	public function diff( RepresentsMixedTypeList $list ): static
	{
		$types = [];
		foreach ( $this->mixedTypes as $type )
		{
			if ( !$this->existsIn( $type, $list ) )
			{
				$types[] = $type;
			}
		}

		return new static( ...$types );
	}

	public function intersect( RepresentsMixedTypeList $list ): static
	{
		$types = [];
		foreach ( $this->mixedTypes as $type )
		{
			if ( $this->existsIn( $type, $list ) )
			{
				$types[] = $type;
			}
		}

		return new static( ...$types );
	}

	/**
	 * @return \Iterator<int, static>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->mixedTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): static
	{
		return new static( ...array_slice( $this->mixedTypes, $index, $count ) );
	}

	protected function validate( RepresentsType ...$types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) )
			{
				throw (new InvalidMixedTypeListException( 'Invalid index in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}

			if ( !static::isValueValid( $type ) )
			{
				throw (new InvalidMixedTypeListException( 'Invalid value in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
