<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidFloatValueListException;
use ComponoKit\Types\Lists\Immutables\Traits\RepresentingImmutableFloatValueList;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableFloatValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;

abstract class AbstractFloatValueList implements RepresentsImmutableFloatValueList
{
	use RepresentingImmutableFloatValueList;

	/**
	 * @param float[] $floats
	 */
	public function __construct( private array $floats = [] )
	{
		$this->validate( $floats );
	}

	abstract protected static function isValueValid( float $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->floats[ $index ] );
	}

	public function get( int $index ): float
	{
		$this->guardIndexExists( $index );

		return $this->floats[ $index ];
	}

	public function set( int $index, float $value ): static
	{
		$floats           = $this->floats;
		$floats[ $index ] = $value;

		return new static( $floats );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->floats;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( float...$types ): static
	{
		$currentTypes = $this->floats;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( float...$types ): static
	{
		$currentTypes = $this->floats;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( array_reverse( $this->floats ) );
	}

	public function shuffle(): static
	{
		$values = $this->floats;
		shuffle( $values );

		return new static( $values );
	}

	public function replace( int $index, float $value ): static
	{
		$values           = $this->floats;
		$values[ $index ] = $value;

		return new static( $values );
	}

	public function unique(): static
	{
		$uniqueValues = new static();

		foreach ( $this->floats as $value )
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
		$types = $this->floats;
		usort(
			$types,
			function ( float $firstValue, float $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);

		return new static( $types );
	}

	public function diff( RepresentsFloatValueList $list ): RepresentsFloatValueList
	{
		return new static(
			array_udiff(
				$this->floats,
				$list,
				function ( float $firstType, float $secondType ): int
				{
					return $firstType === $secondType ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsFloatValueList $list ): RepresentsFloatValueList
	{
		return new static(
			array_uintersect(
				$this->floats,
				$list,
				function ( float $firstType, float $secondType ): int
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
	 * @return \Iterator<int, RepresentsFloatValueList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->floats, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsFloatValueList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->floats, $index, $count, $preserveIndices ) as $values )
		{
			yield new static( $values );
		}
	}

	private function existsIn( float $valueToCheck, RepresentsList $list ): bool
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
			if ( !is_float( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidFloatValueListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
