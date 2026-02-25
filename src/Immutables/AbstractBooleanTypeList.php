<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Exceptions\InvalidBooleanTypeListException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanTypeList;

abstract class AbstractBooleanTypeList implements RepresentsImmutableBooleanTypeList
{
	use RepresentingBooleanTypeList;

	/**
	 * @param RepresentsBoolean[] $booleanTypes
	 */
	public function __construct( private array $booleanTypes = [] )
	{
		$this->validate( $booleanTypes );
	}

	abstract protected static function isValueValid( RepresentsBoolean $type ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->booleanTypes[ $index ] );
	}

	public function get( int $index ): RepresentsBoolean
	{
		$this->guardIndexExists( $index );

		return $this->booleanTypes[ $index ];
	}

	public function set( int $index, RepresentsBoolean $value ): static
	{
		$booleanTypes           = $this->booleanTypes;
		$booleanTypes[ $index ] = $value;

		return new static( $booleanTypes );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->booleanTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( RepresentsBoolean...$types ): static
	{
		$currentTypes = $this->booleanTypes;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( RepresentsBoolean...$types ): static
	{
		$currentTypes = $this->booleanTypes;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsBooleanTypeList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsBooleanTypeList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( array_reverse( $this->booleanTypes ) );
	}

	public function shuffle(): static
	{
		$types = $this->booleanTypes;
		shuffle( $types );

		return new static( $types );
	}

	public function replace( int $index, RepresentsBoolean $type ): static
	{
		$types           = $this->booleanTypes;
		$types[ $index ] = $type;

		return new static( $types );
	}

	public function unique(): static
	{
		$uniqueTypes = new static();

		foreach ( $this->booleanTypes as $type )
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
		$types = $this->booleanTypes;
		usort(
			$types,
			function ( RepresentsBoolean $firstType, RepresentsBoolean $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);

		return new static( $types );
	}

	public function diff( RepresentsBooleanTypeList $list ): RepresentsBooleanTypeList
	{
		return new static(
			array_udiff(
				$this->booleanTypes,
				$list,
				function ( RepresentsBoolean $firstType, RepresentsBoolean $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsBooleanTypeList $list ): RepresentsBooleanTypeList
	{
		return new static(
			array_uintersect(
				$this->booleanTypes,
				$list,
				function ( RepresentsBoolean $firstType, RepresentsBoolean $secondType ): int
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
	 * @return \Iterator<int, RepresentsBooleanTypeList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->booleanTypes, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsBooleanTypeList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->booleanTypes, $index, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	private function existsIn( RepresentsBoolean $typeToCheck, RepresentsList $list ): bool
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

	protected function validate( array $types ): void
	{
		foreach ( $types as $type )
		{
			if ( !($type instanceof RepresentsBoolean) || !static::isValueValid( $type ) )
			{
				throw (new InvalidBooleanTypeListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $type, ] );
			}
		}
	}
}
