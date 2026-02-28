<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidStringValueListException;
use ComponoKit\Types\Lists\Immutables\Traits\RepresentingImmutableStringValueList;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableStringValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringValueList;

abstract class AbstractStringValueList implements RepresentsImmutableStringValueList
{
	use RepresentingImmutableStringValueList;

	public function __construct( string ...$strings )
	{
		$this->validate( $strings );

		$this->strings = $strings;
	}

	abstract protected static function isValueValid( string $value ): bool;

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

		$values = $this->strings;
		array_splice( $values, $index, 1 );

		return new static( ...$values );
	}

	public function replace( int $index, string $value ): static
	{
		$this->guardIndexExists( $index );

		$values           = $this->strings;
		$values[ $index ] = $value;

		return new static( ...$values );
	}

	public function append( string...$values ): static
	{
		return new static( ...$this->strings, ...$values );
	}

	public function prepend( string...$values ): static
	{
		return new static( ...$values, ...$this->strings );
	}

	public function appendList( RepresentsStringValueList|RepresentsStringTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...$list->toNativeType() );
	}

	public function prependList( RepresentsStringValueList|RepresentsStringTypeList $list ): static
	{
		return new static( ...$list->toNativeType(), ...$this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( ...array_reverse( $this->strings ) );
	}

	public function shuffle(): static
	{
		$values = $this->strings;
		shuffle( $values );

		return new static( ...$values );
	}

	public function unique(): static
	{
		$uniqueValues = new static();

		foreach ( $this->strings as $value )
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
		$values = $this->strings;
		usort(
			$values,
			function ( int $firstValue, int $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);

		return new static( ...$values );
	}

	public function diff( RepresentsStringValueList $list ): static
	{
		return new static(
			...array_udiff(
				$this->strings,
				$list->toNativeType(),
				function ( int $firstType, int $secondType ): int
				{
					return $firstType === $secondType ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsStringValueList $list ): static
	{
		return new static(
			...array_uintersect(
				$this->strings,
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
		foreach ( array_chunk( $this->strings, $count ) as $values )
		{
			yield new static( ...$values );
		}
	}

	public function slice( int $index, ?int $count = null ): static
	{
		return new static( ...array_slice( $this->strings, $index, $count ) );
	}

	private function existsIn( string $valueToCheck, RepresentsList $list ): bool
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
			if ( !is_string( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidStringValueListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
