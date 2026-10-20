<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\Factories\BuildsFloatTypes;
use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Lists\Exceptions\InvalidFloatTypeListException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatValueList;
use ComponoKit\Types\Lists\Traits\RepresentingFloatTypeList;

abstract class AbstractFloatTypeList implements RepresentsImmutableFloatTypeList
{
	use RepresentingFloatTypeList;

	public function __construct( RepresentsFloat ...$floatTypes )
	{
		$this->validate( ...$floatTypes );

		$this->floatTypes = $floatTypes;
	}

	abstract protected static function isValueValid( RepresentsFloat $type ): bool;

	public static function fromValueList( RepresentsFloatValueList $list, BuildsFloatTypes $factory ): static
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

		$types = $this->floatTypes;
		array_splice( $types, $index, 1 );

		return new static( ...$types );
	}

	public function replace( int $index, RepresentsFloat $type ): static
	{
		$this->guardIndexExists( $index );

		$types           = $this->floatTypes;
		$types[ $index ] = $type;

		return new static( ...$types );
	}

	public function append( RepresentsFloat ...$types ): static
	{
		return new static( ...$this->floatTypes, ...$types );
	}

	public function prepend( RepresentsFloat ...$types ): static
	{
		return new static( ...$types, ...$this->floatTypes );
	}

	public function appendList( RepresentsFloatTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...$list->toNativeType() );
	}

	public function prependList( RepresentsFloatTypeList $list ): static
	{
		return new static( ...$list->toNativeType(), ...$this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( ...array_reverse( $this->floatTypes ) );
	}

	public function shuffle(): static
	{
		$types = $this->floatTypes;
		shuffle( $types );

		return new static( ...$types );
	}

	public function unique(): static
	{
		$uniqueTypes = new static();

		foreach ( $this->floatTypes as $type )
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
		$types = $this->floatTypes;
		usort( $types, $comparer->compare( ... ) );

		return new static( ...$types );
	}

	public function diff( RepresentsFloatTypeList $list ): static
	{
		$types = [];
		foreach ( $this->floatTypes as $type )
		{
			if ( !$this->existsIn( $type, $list ) )
			{
				$types[] = $type;
			}
		}

		return new static( ...$types );
	}

	public function intersect( RepresentsFloatTypeList $list ): static
	{
		$types = [];
		foreach ( $this->floatTypes as $type )
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
		foreach ( array_chunk( $this->floatTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): static
	{
		return new static( ...array_slice( $this->floatTypes, $index, $count ) );
	}

	protected function validate( RepresentsFloat ...$types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) )
			{
				throw new InvalidFloatTypeListException( 'Invalid index in ' . static::class )->setContext( [ 'index' => $index, 'value' => $type, ] );
			}

			if ( !static::isValueValid( $type ) )
			{
				throw new InvalidFloatTypeListException( 'Invalid value in ' . static::class )->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
