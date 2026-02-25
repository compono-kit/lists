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

	/**
	 * @param string[] $strings
	 */
	public function __construct( private array $strings = [] )
	{
		$this->validate( $strings );
	}

	abstract protected static function isValueValid( string $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->strings[ $index ] );
	}

	public function get( int $index ): string
	{
		$this->guardIndexExists( $index );

		return $this->strings[ $index ];
	}

	public function set( int $index, string $value ): static
	{
		$strings           = $this->strings;
		$strings[ $index ] = $value;

		return new static( $strings );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->strings;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( string...$types ): static
	{
		$currentTypes = $this->strings;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( string...$types ): static
	{
		$currentTypes = $this->strings;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsStringValueList|RepresentsStringTypeList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsStringValueList|RepresentsStringTypeList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( array_reverse( $this->strings ) );
	}

	public function shuffle(): static
	{
		$values = $this->strings;
		shuffle( $values );

		return new static( $values );
	}

	public function replace( int $index, string $value ): static
	{
		$values           = $this->strings;
		$values[ $index ] = $value;

		return new static( $values );
	}

	public function unique(): static
	{
		$uniqueValues = new static();

		foreach ( $this->strings as $value )
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
		$types = $this->strings;
		usort(
			$types,
			function ( int $firstValue, int $secondValue ) use ( $comparer ): int
			{
				return $comparer->compare( $firstValue, $secondValue );
			}
		);

		return new static( $types );
	}

	public function diff( RepresentsStringValueList $list ): RepresentsStringValueList
	{
		return new static(
			array_udiff(
				$this->strings,
				$list,
				function ( int $firstType, int $secondType ): int
				{
					return $firstType === $secondType ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsStringValueList $list ): RepresentsStringValueList
	{
		return new static(
			array_uintersect(
				$this->strings,
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
	 * @return \Iterator<int, RepresentsStringValueList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->strings, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsStringValueList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->strings, $index, $count, $preserveIndices ) as $values )
		{
			yield new static( $values );
		}
	}

	private function existsIn( string $valueToCheck, RepresentsList $list ): bool
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
			if ( !is_string( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidStringValueListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
