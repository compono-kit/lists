<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidIntegerValueListException;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableIntegerValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;
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

	public function append( int ...$values ): static
	{
		return new static( ...$this->integers, ...$values );
	}

	public function prepend( int ...$values ): static
	{
		return new static( ...$values, ...$this->integers );
	}

	public function appendList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...($list instanceof RepresentsIntegerTypeList ? $list->toIntegers() : $list->toNativeType()) );
	}

	public function prependList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): static
	{
		return new static( ...($list instanceof RepresentsIntegerTypeList ? $list->toIntegers() : $list->toNativeType()), ...$this->toNativeType() );
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
		usort( $values, [ $comparer, 'compare' ] );

		return new static( ...$values );
	}

	public function diff( RepresentsIntegerValueList $list ): static
	{
		$values = [];
		foreach ( $this->integers as $value )
		{
			if ( !$this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	public function intersect( RepresentsIntegerValueList $list ): static
	{
		$values = [];
		foreach ( $this->integers as $value )
		{
			if ( $this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	/**
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
				throw (new InvalidIntegerValueListException( 'Invalid value in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
