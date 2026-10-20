<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidBooleanValueListException;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableBooleanValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanValueList;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanValueList;

abstract class AbstractBooleanValueList implements RepresentsImmutableBooleanValueList
{
	use RepresentingBooleanValueList;

	public function __construct( bool ...$booleans )
	{
		$this->validate( ...$booleans );

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

	public function append( bool ...$values ): static
	{
		return new static( ...$this->booleans, ...$values );
	}

	public function prepend( bool ...$values ): static
	{
		return new static( ...$values, ...$this->booleans );
	}

	public function appendList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...($list instanceof RepresentsBooleanTypeList ? $list->toBooleans() : $list->toNativeType()) );
	}

	public function prependList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): static
	{
		return new static( ...($list instanceof RepresentsBooleanTypeList ? $list->toBooleans() : $list->toNativeType()), ...$this->toNativeType() );
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
		usort( $values, $comparer->compare( ... ) );

		return new static( ...$values );
	}

	public function diff( RepresentsBooleanValueList $list ): static
	{
		$values = [];
		foreach ( $this->booleans as $value )
		{
			if ( !$this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	public function intersect( RepresentsBooleanValueList $list ): static
	{
		$values = [];
		foreach ( $this->booleans as $value )
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
		foreach ( array_chunk( $this->booleans, $count ) as $values )
		{
			yield new static( ...$values );
		}
	}

	public function slice( int $index, ?int $count = null ): static
	{
		return new static( ...array_slice( $this->booleans, $index, $count ) );
	}

	protected function validate( bool ...$values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) )
			{
				throw new InvalidBooleanValueListException( 'Invalid index in ' . static::class )->setContext( [ 'index' => $index, 'value' => $value, ] );
			}

			if ( !static::isValueValid( $value ) )
			{
				throw new InvalidBooleanValueListException( 'Invalid value in ' . static::class )->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
