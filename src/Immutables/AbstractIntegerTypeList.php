<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\Factories\BuildsIntegerTypes;
use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Exceptions\InvalidIntegerTypeListException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerTypeList;

abstract class AbstractIntegerTypeList implements RepresentsImmutableIntegerTypeList
{
	use RepresentingIntegerTypeList;

	public function __construct( RepresentsInteger ...$integerTypes )
	{
		$this->validate( ...$integerTypes );

		$this->integerTypes = $integerTypes;
	}

	abstract protected static function isValueValid( RepresentsInteger $type ): bool;

	public static function fromValueList( RepresentsIntegerValueList $list, BuildsIntegerTypes $factory ): static
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

		$types = $this->integerTypes;
		array_splice( $types, $index, 1 );

		return new static( ...$types );
	}

	public function replace( int $index, RepresentsInteger $type ): static
	{
		$this->guardIndexExists( $index );

		$types           = $this->integerTypes;
		$types[ $index ] = $type;

		return new static( ...$types );
	}

	public function append( RepresentsInteger...$types ): static
	{
		return new static( ...$this->integerTypes, ...$types );
	}

	public function prepend( RepresentsInteger...$types ): static
	{
		return new static( ...$types, ...$this->integerTypes );
	}

	public function appendList( RepresentsIntegerTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...$list->toNativeType() );
	}

	public function prependList( RepresentsIntegerTypeList $list ): static
	{
		return new static( ...$list->toNativeType(), ...$this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( ...array_reverse( $this->integerTypes ) );
	}

	public function shuffle(): static
	{
		$types = $this->integerTypes;
		shuffle( $types );

		return new static( ...$types );
	}

	public function unique(): static
	{
		$uniqueTypes = new static();

		foreach ( $this->integerTypes as $type )
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
		$types = $this->integerTypes;
		usort(
			$types,
			function ( RepresentsInteger $firstType, RepresentsInteger $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);

		return new static( ...$types );
	}

	public function diff( RepresentsIntegerTypeList $list ): static
	{
		return new static(
			...array_udiff(
				$this->integerTypes,
				$list->toNativeType(),
				function ( RepresentsInteger $firstType, RepresentsInteger $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsIntegerTypeList $list ): static
	{
		return new static(
			...array_uintersect(
				$this->integerTypes,
				$list->toNativeType(),
				function ( RepresentsInteger $firstType, RepresentsInteger $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	/**
	 * @param int $count
	 *
	 * @return \Iterator<int, static>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->integerTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): static
	{
		return new static( ...array_slice( $this->integerTypes, $index, $count ) );
	}

	protected function validate( RepresentsInteger ...$types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) )
			{
				throw (new InvalidIntegerTypeListException( 'Invalid index in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}

			if ( !static::isValueValid( $type ) )
			{
				throw (new InvalidIntegerTypeListException( 'Invalid value in ' . static::class ))->setContext( [ 'value' => $type, ] );
			}
		}
	}

	private function existsIn( RepresentsInteger $typeToCheck, RepresentsList $list ): bool
	{
		foreach ( $list as $type )
		{
			if ( $type->equals( $typeToCheck ) )
			{
				return true;
			}
		}

		return false;
	}
}
