<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidIntegerValueListException;
use ComponoKit\Types\Lists\Immutables\Traits\RepresentingImmutableIntegerValueList;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableIntegerValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;

abstract class AbstractIntegerValueList implements RepresentsImmutableIntegerValueList
{
	use RepresentingImmutableIntegerValueList;

	/**
	 * @param int[] $integers
	 */
	public function __construct( private array $integers = [] )
	{
		$this->validate( $integers );
	}

	abstract protected static function isValueValid( int $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->integers[ $index ] );
	}

	public function get( int $index ): int
	{
		$this->guardIndexExists( $index );

		return $this->integers[ $index ];
	}

	public function set( int $index, int $value ): static
	{
		$integers           = $this->integers;
		$integers[ $index ] = $value;

		return new static( $integers );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->integers;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( int...$types ): static
	{
		$currentTypes = $this->integers;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( int...$types ): static
	{
		$currentTypes = $this->integers;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( array_reverse( $this->integers ) );
	}

	public function shuffle(): static
	{
		$values = $this->integers;
		shuffle( $values );

		return new static( $values );
	}

	public function replace( int $index, int $value ): static
	{
		$values           = $this->integers;
		$values[ $index ] = $value;

		return new static( $values );
	}

	public function unique(): static
	{
		$uniqueValues = new static();

		foreach ( $this->integers as $value )
		{
			if ( !$this->existsIn( $value, $uniqueValues ) )
			{
				$uniqueValues->append( $value );
			}
		}

		return $uniqueValues;
	}

	public function sort( ComparesValues $comparer ): static
	{
		$types = $this->integers;
		usort(
			$types,
			function ( int $firstValue, int $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);

		return new static( $types );
	}

	public function diff( RepresentsIntegerValueList $list ): RepresentsIntegerValueList
	{
		return new static(
			array_udiff(
				$this->integers,
				$list,
				function ( int $firstType, int $secondType ): int
				{
					return $firstType === $secondType ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsIntegerValueList $list ): RepresentsIntegerValueList
	{
		return new static(
			array_uintersect(
				$this->integers,
				$list,
				function ( int $firstType, int $secondType ): int
				{
					return $firstType === $secondType ? 1 : 0;
				}
			),
		);
	}

	/**
	 * @param int  $count
	 * @param bool $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsIntegerValueList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->integers, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsIntegerValueList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->integers, $index, $count, $preserveIndices ) as $values )
		{
			yield new static( $values );
		}
	}

	private function existsIn( int $valueToCheck, RepresentsList $list ): bool
	{
		foreach ( $list as $type )
		{
			if ( $type->equals( $valueToCheck ) )
			{
				return true;
			}
		}

		return false;
	}

	protected function validate( array $values ): void
	{
		foreach ( $values as $value )
		{
			if ( !is_int( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidIntegerValueListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
