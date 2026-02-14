<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Exceptions\InvalidIntegerTypeListException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerTypeList;

abstract class AbstractIntegerTypeList implements RepresentsImmutableIntegerTypeList
{
	use RepresentingIntegerTypeList;

	/**
	 * @param RepresentsInteger[] $integerTypes
	 */
	public function __construct( private array $integerTypes = [] )
	{
		$this->validate( $integerTypes );
	}

	abstract protected static function isValueValid( RepresentsInteger $type ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->integerTypes[ $index ] );
	}

	public function get( int $index ): RepresentsInteger
	{
		$this->guardIndexExists( $index );

		return $this->integerTypes[ $index ];
	}

	public function set( int $index, RepresentsInteger $value ): static
	{
		$integerTypes           = $this->integerTypes;
		$integerTypes[ $index ] = $value;

		return new static( $integerTypes );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->integerTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( RepresentsInteger...$types ): static
	{
		$currentTypes = $this->integerTypes;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( RepresentsInteger...$types ): static
	{
		$currentTypes = $this->integerTypes;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsIntegerTypeList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsIntegerTypeList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( array_reverse( $this->integerTypes ) );
	}

	public function shuffle(): static
	{
		$types = $this->integerTypes;
		shuffle( $types );

		return new static( $types );
	}

	public function replace( int $index, RepresentsInteger $type ): static
	{
		$types           = $this->integerTypes;
		$types[ $index ] = $type;

		return new static( $types );
	}

	public function unique(): static
	{
		$uniqueTypes = new static();

		foreach ( $this->integerTypes as $type )
		{
			if ( !$this->existsIn( $type, $uniqueTypes ) )
			{
				$uniqueTypes->append( $type );
			}
		}

		return $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): static
	{
		$types = $this->integerTypes;
		usort(
			$types,
			function ( RepresentsInteger $firstType, RepresentsInteger $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);

		return new static( $types );
	}

	public function diff( RepresentsIntegerTypeList $list ): RepresentsIntegerTypeList
	{
		return new static(
			array_udiff(
				$this->integerTypes,
				$list,
				function ( RepresentsInteger $firstType, RepresentsInteger $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsIntegerTypeList $list ): RepresentsIntegerTypeList
	{
		return new static(
			array_uintersect(
				$this->integerTypes,
				$list,
				function ( RepresentsInteger $firstType, RepresentsInteger $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 1 : 0;
				}
			),
		);
	}

	/**
	 * @param int  $count
	 * @param bool $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsIntegerTypeList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->integerTypes, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsIntegerTypeList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		return new static( array_slice( $this->integerTypes, $index, $count, $preserveIndices ) );
	}

	protected function validate( array $types ): void
	{
		foreach ( $types as $type )
		{
			if ( !($type instanceof RepresentsInteger) || !static::isValueValid( $type ) )
			{
				throw (new InvalidIntegerTypeListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $type, ] );
			}
		}
	}

	private function existsIn( RepresentsInteger $typeToCheck, RepresentsList $list ): bool
	{
		foreach ( $list as $type )
		{
			if ( $type->equals( $typeToCheck ) )
			{
				return true;
			}
		}

		return false;
	}
}
