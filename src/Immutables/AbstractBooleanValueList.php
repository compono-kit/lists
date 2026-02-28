<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidBooleanTypeListException;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableBooleanValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanValueList;

abstract class AbstractBooleanValueList implements RepresentsImmutableBooleanValueList
{
	use RepresentingBooleanValueList;

	/**
	 * @param bool[] $booleans
	 */
	public function __construct( bool ...$booleans )
	{
		$this->validate( $booleans );

		$this->booleans = $booleans;
	}

	abstract protected static function isValueValid( bool $value ): bool;

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

		$values = $this->booleans;
		array_splice( $values, $index, 1 );

		return new static( ...$values );
	}

	public function replace( int $index, bool $value ): static
	{
		$this->guardIndexExists( $index );

		$values           = $this->booleans;
		$values[ $index ] = $value;

		return new static( ...$values );
	}

	public function append( bool...$values ): static
	{
		return new static( ...$this->booleans, ...$values );
	}

	public function prepend( bool...$values ): static
	{
		return new static( ...$values, ...$this->booleans );
	}

	public function appendList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...$list->toNativeType() );
	}

	public function prependList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): static
	{
		return new static( ...$list->toNativeType(), ...$this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( ...array_reverse( $this->booleans ) );
	}

	public function shuffle(): static
	{
		$values = $this->booleans;
		shuffle( $values );

		return new static( ...$values );
	}

	public function unique(): static
	{
		$uniqueValues = new static();

		foreach ( $this->booleans as $value )
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
		$values = $this->booleans;
		usort(
			$values,
			function ( bool $firstValue, bool $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);

		return new static( ...$values );
	}

	public function diff( RepresentsBooleanValueList $list ): static
	{
		return new static(
			...array_udiff(
				$this->booleans,
				$list->toNativeType(),
				function ( bool $firstType, bool $secondType ): int
				{
					return $firstType === $secondType ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsBooleanValueList $list ): static
	{
		return new static(
			...array_uintersect(
				$this->booleans,
				$list->toNativeType(),
				function ( bool $firstType, bool $secondType ): int
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
		foreach ( array_chunk( $this->booleans, $count ) as $values )
		{
			yield new static( ...$values );
		}
	}

	public function slice( int $index, ?int $count = null ): static
	{
		return new static( ...array_slice( $this->booleans, $index, $count ) );
	}

	private function existsIn( bool $valueToCheck, RepresentsList $list ): bool
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

	protected function validate( array $values ): void
	{
		foreach ( $values as $value )
		{
			if ( !is_bool( $value ) )
			{
				throw (new InvalidBooleanTypeListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
