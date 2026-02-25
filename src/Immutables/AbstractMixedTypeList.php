<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\InvalidMixedTypeListException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingMixedTypeList;

abstract class AbstractMixedTypeList implements RepresentsMixedTypeList
{
	use RepresentingMixedTypeList;

	/**
	 * @param RepresentsType[] $mixedTypes
	 */
	public function __construct( private array $mixedTypes = [] )
	{
		$this->validate( $mixedTypes );
	}

	abstract protected static function isValueValid( RepresentsType $type ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->mixedTypes[ $index ] );
	}

	public function get( int $index ): RepresentsType
	{
		$this->guardIndexExists( $index );

		return $this->mixedTypes[ $index ];
	}

	public function set( int $index, RepresentsType $value ): static
	{
		$mixedTypes           = $this->mixedTypes;
		$mixedTypes[ $index ] = $value;

		return new static( $mixedTypes );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->mixedTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( int|float|string|bool|array...$types ): static
	{
		$currentTypes = $this->mixedTypes;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( int|float|string|bool|array...$types ): static
	{
		$currentTypes = $this->mixedTypes;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsMixedTypeList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsMixedTypeList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( array_reverse( $this->mixedTypes ) );
	}

	public function shuffle(): static
	{
		$types = $this->mixedTypes;
		shuffle( $types );

		return new static( $types );
	}

	public function replace( int $index, RepresentsType $type ): static
	{
		$types           = $this->mixedTypes;
		$types[ $index ] = $type;

		return new static( $types );
	}

	public function unique(): static
	{
		$uniqueTypes = new static();

		foreach ( $this->mixedTypes as $type )
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
		$types = $this->mixedTypes;
		usort(
			$types,
			function ( RepresentsType $firstType, RepresentsType $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);

		return new static( $types );
	}

	public function diff( RepresentsMixedTypeList $list ): RepresentsMixedTypeList
	{
		return new static(
			array_udiff(
				$this->mixedTypes,
				$list,
				function ( RepresentsType $firstType, RepresentsType $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsMixedTypeList $list ): RepresentsMixedTypeList
	{
		return new static(
			array_uintersect(
				$this->mixedTypes,
				$list,
				function ( RepresentsType $firstType, RepresentsType $secondType ): int
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
	 * @return \Iterator<int, RepresentsMixedTypeList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->mixedTypes, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsMixedTypeList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->mixedTypes, $index, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	protected function validate( array $types ): void
	{
		foreach ( $types as $type )
		{
			if ( !($type instanceof RepresentsType) || !static::isValueValid( $type ) )
			{
				throw (new InvalidMixedTypeListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $type, ] );
			}
		}
	}

	private function existsIn( RepresentsType $typeToCheck, RepresentsList $list ): bool
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
