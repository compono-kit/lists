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

	/**
	 * @param array<int, int|float|string|bool|array> $mixedValues
	 */
	public function __construct( private array $mixedValues = [] )
	{
		$this->validate( $mixedValues );
	}

	abstract protected static function isValueValid( int|float|string|bool|array $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->mixedValues[ $index ] );
	}

	public function get( int $index ): int
	{
		$this->guardIndexExists( $index );

		return $this->mixedValues[ $index ];
	}

	public function set( int $index, int|float|string|bool|array $value ): static
	{
		$mixedValues           = $this->mixedValues;
		$mixedValues[ $index ] = $value;

		return new static( $mixedValues );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->mixedValues;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( int|float|string|bool|array...$types ): static
	{
		$currentTypes = $this->mixedValues;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( int|float|string|bool|array...$types ): static
	{
		$currentTypes = $this->mixedValues;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( array_reverse( $this->mixedValues ) );
	}

	public function shuffle(): static
	{
		$values = $this->mixedValues;
		shuffle( $values );

		return new static( $values );
	}

	public function replace( int $index, int|float|string|bool|array $value ): static
	{
		$values           = $this->mixedValues;
		$values[ $index ] = $value;

		return new static( $values );
	}

	public function unique(): static
	{
		$uniqueValues = new static();

		foreach ( $this->mixedValues as $value )
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
		$types = $this->mixedValues;
		usort(
			$types,
			function ( int $firstValue, int $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);

		return new static( $types );
	}

	public function diff( RepresentsMixedValueList $list ): RepresentsMixedValueList
	{
		return new static(
			array_udiff(
				$this->mixedValues,
				$list,
				function ( int $firstType, int $secondType ): int
				{
					return $firstType === $secondType ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsMixedValueList $list ): RepresentsMixedValueList
	{
		return new static(
			array_uintersect(
				$this->mixedValues,
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
	 * @return \Iterator<int, RepresentsMixedValueList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->mixedValues, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsMixedValueList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->mixedValues, $index, $count, $preserveIndices ) as $values )
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
			if ( (!is_scalar( $value ) && !is_array( $value )) || !static::isValueValid( $value ) )
			{
				throw (new InvalidMixedValueListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
