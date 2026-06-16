<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidFloatValueListException;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableFloatValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Traits\RepresentingFloatValueList;

abstract class AbstractFloatValueList implements RepresentsImmutableFloatValueList
{
	use RepresentingFloatValueList;

	public function __construct( float ...$floats )
	{
		$this->validate( ...$floats );

		$this->floats = $floats;
	}

	abstract protected static function isValueValid( float $value ): bool;

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

		$values = $this->floats;
		array_splice( $values, $index, 1 );

		return new static( ...$values );
	}

	public function replace( int $index, float $value ): static
	{
		$this->guardIndexExists( $index );

		$values           = $this->floats;
		$values[ $index ] = $value;

		return new static( ...$values );
	}

	public function append( float...$values ): static
	{
		return new static( ...$this->floats, ...$values );
	}

	public function prepend( float...$values ): static
	{
		return new static( ...$values, ...$this->floats );
	}

	public function appendList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...$list->toNativeType() );
	}

	public function prependList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): static
	{
		return new static( ...$list->toNativeType(), ...$this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( ...array_reverse( $this->floats ) );
	}

	public function shuffle(): static
	{
		$values = $this->floats;
		shuffle( $values );

		return new static( ...$values );
	}

	public function unique(): static
	{
		$uniqueValues = new static();

		foreach ( $this->floats as $value )
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
		$values = $this->floats;
		usort(
			$values,
			function ( float $firstValue, float $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);

		return new static( ...$values );
	}

	public function diff( RepresentsFloatValueList $list ): static
	{
		return new static(
			...array_udiff(
				$this->floats,
				$list->toNativeType(),
				function ( float $firstType, float $secondType ): int
				{
					return $firstType === $secondType ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsFloatValueList $list ): static
	{
		return new static(
			...array_uintersect(
				$this->floats,
				$list->toNativeType(),
				function ( float $firstType, float $secondType ): int
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
		foreach ( array_chunk( $this->floats, $count ) as $values )
		{
			yield new static( ...$values );
		}
	}

	public function slice( int $index, ?int $count = null ): static
	{
		return new static( ...array_slice( $this->floats, $index, $count ) );
	}

	protected function validate( float ...$values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) )
			{
				throw (new InvalidFloatValueListException( 'Invalid index in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}

			if ( !static::isValueValid( $value ) )
			{
				throw (new InvalidFloatValueListException( 'Invalid value in ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}

	private function existsIn( float $valueToCheck, RepresentsList $list ): bool
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
