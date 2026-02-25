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
	public function __construct( private array $booleans = [] )
	{
		$this->validate( $booleans );
	}

	abstract protected static function isValueValid( bool $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->booleans[ $index ] );
	}

	public function get( int $index ): bool
	{
		$this->guardIndexExists( $index );

		return $this->booleans[ $index ];
	}

	public function set( int $index, bool $value ): static
	{
		$booleans           = $this->booleans;
		$booleans[ $index ] = $value;

		return new static( $booleans );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->booleans;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( bool...$types ): static
	{
		$currentTypes = $this->booleans;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( bool...$types ): static
	{
		$currentTypes = $this->booleans;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( array_reverse( $this->booleans ) );
	}

	public function shuffle(): static
	{
		$values = $this->booleans;
		shuffle( $values );

		return new static( $values );
	}

	public function replace( int $index, bool $value ): static
	{
		$values           = $this->booleans;
		$values[ $index ] = $value;

		return new static( $values );
	}

	public function unique(): static
	{
		$uniqueValues = new static();

		foreach ( $this->booleans as $value )
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
		$types = $this->booleans;
		usort(
			$types,
			function ( bool $firstValue, bool $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);

		return new static( $types );
	}

	public function diff( RepresentsBooleanValueList $list ): RepresentsBooleanValueList
	{
		return new static(
			array_udiff(
				$this->booleans,
				$list,
				function ( bool $firstType, bool $secondType ): int
				{
					return $firstType === $secondType ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsBooleanValueList $list ): RepresentsBooleanValueList
	{
		return new static(
			array_uintersect(
				$this->booleans,
				$list,
				function ( bool $firstType, bool $secondType ): int
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
	 * @return \Iterator<int, RepresentsBooleanValueList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->booleans, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsBooleanValueList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->booleans, $index, $count, $preserveIndices ) as $values )
		{
			yield new static( $values );
		}
	}

	private function existsIn( bool $valueToCheck, RepresentsList $list ): bool
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
			if ( !is_bool( $value ) )
			{
				throw (new InvalidBooleanTypeListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
