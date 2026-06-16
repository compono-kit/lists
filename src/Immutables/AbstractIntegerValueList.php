<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidIntegerValueListException;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableIntegerValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerValueList;

abstract class AbstractIntegerValueList implements RepresentsImmutableIntegerValueList
{
	use RepresentingIntegerValueList;

	public function __construct( int ...$integers )
	{
		$this->validate( ...$integers );

		$this->integers = $integers;
	}

	abstract protected static function isValueValid( int $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->integers;
		array_splice( $values, $index, 1 );

		return new static( ...$values );
	}

	public function replace( int $index, int $value ): static
	{
		$this->guardIndexExists( $index );

		$values           = $this->integers;
		$values[ $index ] = $value;

		return new static( ...$values );
	}

	public function append( int...$values ): static
	{
		return new static( ...$this->integers, ...$values );
	}

	public function prepend( int...$values ): static
	{
		return new static( ...$values, ...$this->integers );
	}

	public function appendList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...$list->toNativeType() );
	}

	public function prependList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): static
	{
		return new static( ...$list->toNativeType(), ...$this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( ...array_reverse( $this->integers ) );
	}

	public function shuffle(): static
	{
		$values = $this->integers;
		shuffle( $values );

		return new static( ...$values );
	}

	public function unique(): static
	{
		$uniqueValues = new static();

		foreach ( $this->integers as $value )
		{
			if ( !$this->existsIn( $value, $uniqueValues ) )
			{
				$uniqueValues = $uniqueValues->append( $value );
			}
		}

		return $uniqueValues;
	}

	public function sort( ComparesValues $comparer ): static
	{
		$values = $this->integers;
		usort(
			$values,
			function ( int $firstValue, int $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);

		return new static( ...$values );
	}

	public function diff( RepresentsIntegerValueList $list ): static
	{
		return new static(
			...array_udiff(
				$this->integers,
				$list->toNativeType(),
				function ( int $firstType, int $secondType ): int
				{
					return $firstType === $secondType ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsIntegerValueList $list ): static
	{
		return new static(
			...array_uintersect(
				$this->integers,
				$list->toNativeType(),
				function ( int $firstType, int $secondType ): int
				{
					return $firstType === $secondType ? 0 : 1;
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
		foreach ( array_chunk( $this->integers, $count ) as $values )
		{
			yield new static( ...$values );
		}
	}

	public function slice( int $index, ?int $count = null ): static
	{
		return new static( ...array_slice( $this->integers, $index, $count ) );
	}

	protected function validate( int ...$values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) )
			{
				throw (new InvalidIntegerValueListException( 'Invalid index in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}

			if ( !static::isValueValid( $value ) )
			{
				throw (new InvalidIntegerValueListException( 'Invalid value in ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}

	private function existsIn( int $valueToCheck, RepresentsList $list ): bool
	{
		foreach ( $list as $value )
		{
			if ( $value === $valueToCheck )
			{
				return true;
			}
		}

		return false;
	}
}
