<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\Factories\BuildsBooleanTypes;
use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Exceptions\InvalidBooleanTypeListException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanTypeList;

abstract class AbstractBooleanTypeList implements RepresentsImmutableBooleanTypeList
{
	use RepresentingBooleanTypeList;

	public function __construct( RepresentsBoolean ...$booleanTypes )
	{
		$this->validate( $booleanTypes );

		$this->booleanTypes = $booleanTypes;
	}

	abstract protected static function isValueValid( RepresentsBoolean $type ): bool;

	public static function fromValueList( RepresentsBooleanValueList $list, BuildsBooleanTypes $factory ): static
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

		$types = $this->booleanTypes;
		array_splice( $types, $index, 1 );

		return new static( ...$types );
	}

	public function replace( int $index, RepresentsBoolean $type ): static
	{
		$this->guardIndexExists( $index );

		$types           = $this->booleanTypes;
		$types[ $index ] = $type;

		return new static( ...$types );
	}

	public function append( RepresentsBoolean...$types ): static
	{
		return new static( ...$this->booleanTypes, ...$types );
	}

	public function prepend( RepresentsBoolean...$types ): static
	{
		return new static( ...$types, ...$this->booleanTypes );
	}

	public function appendList( RepresentsBooleanTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...$list->toNativeType() );
	}

	public function prependList( RepresentsBooleanTypeList $list ): static
	{
		return new static( ...$list->toNativeType(), ...$this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( ...array_reverse( $this->booleanTypes ) );
	}

	public function shuffle(): static
	{
		$types = $this->booleanTypes;
		shuffle( $types );

		return new static( ...$types );
	}

	public function unique(): static
	{
		$uniqueTypes = new static();

		foreach ( $this->booleanTypes as $type )
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
		$types = $this->booleanTypes;
		usort(
			$types,
			function ( RepresentsBoolean $firstType, RepresentsBoolean $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);

		return new static( ...$types );
	}

	public function diff( RepresentsBooleanTypeList $list ): static
	{
		return new static(
			...array_udiff(
				$this->booleanTypes,
				$list->toNativeType(),
				function ( RepresentsBoolean $firstType, RepresentsBoolean $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsBooleanTypeList $list ): static
	{
		return new static(
			...array_uintersect(
				$this->booleanTypes,
				$list->toNativeType(),
				function ( RepresentsBoolean $firstType, RepresentsBoolean $secondType ): int
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
		foreach ( array_chunk( $this->booleanTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): static
	{
		return new static( ...array_slice( $this->booleanTypes, $index, $count ) );
	}

	private function existsIn( RepresentsBoolean $typeToCheck, RepresentsList $list ): bool
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

	protected function validate( array $types ): void
	{
		foreach ( $types as $type )
		{
			if ( !($type instanceof RepresentsBoolean) || !static::isValueValid( $type ) )
			{
				throw (new InvalidBooleanTypeListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $type, ] );
			}
		}
	}
}
