<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidMixedValueListException;
use ComponoKit\Types\Lists\Immutables\Traits\RepresentingImmutableMixedValueList;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableMixedValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedValueList;

abstract class AbstractMixedValueList implements RepresentsImmutableMixedValueList
{
	use RepresentingImmutableMixedValueList;

	public function __construct( int|float|string|bool|array ...$mixedValues )
	{
		$this->validate( $mixedValues );

		$this->mixedValues = $mixedValues;
	}

	abstract protected static function isValueValid( int|float|string|bool|array $value ): bool;

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

		$values = $this->mixedValues;
		array_splice( $values, $index, 1 );

		return new static( ...$values );
	}

	public function replace( int $index, int|float|string|bool|array $value ): static
	{
		$this->guardIndexExists( $index );

		$values           = $this->mixedValues;
		$values[ $index ] = $value;

		return new static( ...$values );
	}

	public function append( int|float|string|bool|array...$values ): static
	{
		return new static( ...$this->mixedValues, ...$values );
	}

	public function prepend( int|float|string|bool|array...$values ): static
	{
		return new static( ...$values, ...$this->mixedValues );
	}

	public function appendList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...$list->toNativeType() );
	}

	public function prependList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): static
	{
		return new static( ...$list->toNativeType(), ...$this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( ...array_reverse( $this->mixedValues ) );
	}

	public function shuffle(): static
	{
		$values = $this->mixedValues;
		shuffle( $values );

		return new static( ...$values );
	}

	public function unique(): static
	{
		$uniqueValues = new static();

		foreach ( $this->mixedValues as $value )
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
		$values = $this->mixedValues;
		usort(
			$values,
			function ( int $firstValue, int $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);

		return new static( ...$values );
	}

	public function diff( RepresentsMixedValueList $list ): static
	{
		return new static(
			...array_udiff(
				$this->mixedValues,
				$list->toNativeType(),
				function ( int $firstType, int $secondType ): int
				{
					return $firstType === $secondType ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsMixedValueList $list ): static
	{
		return new static(
			...array_uintersect(
				$this->mixedValues,
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
		foreach ( array_chunk( $this->mixedValues, $count ) as $values )
		{
			yield new static( ...$values );
		}
	}

	public function slice( int $index, ?int $count = null ): static
	{
		return new static( ...array_slice( $this->mixedValues, $index, $count ) );
	}

	protected function validate( array $values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) )
			{
				throw (new InvalidMixedValueListException( 'Invalid index in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}

			if ( (!is_scalar( $value ) && !is_array( $value )) || !static::isValueValid( $value ) )
			{
				throw (new InvalidMixedValueListException( 'Invalid value in ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}

	private function existsIn( int $valueToCheck, RepresentsList $list ): bool
	{
		foreach ( $list as $value )
		{
			if ( $value->equals( $valueToCheck ) )
			{
				return true;
			}
		}

		return false;
	}
}
