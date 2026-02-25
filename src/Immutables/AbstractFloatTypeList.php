<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Lists\Exceptions\InvalidFloatTypeListException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Traits\RepresentingFloatTypeList;

abstract class AbstractFloatTypeList implements RepresentsImmutableFloatTypeList
{
	use RepresentingFloatTypeList;

	/**
	 * @param RepresentsFloat[] $floatTypes
	 */
	public function __construct( private array $floatTypes = [] )
	{
		$this->validate( $floatTypes );
	}

	abstract protected static function isValueValid( RepresentsFloat $type ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->floatTypes[ $index ] );
	}

	public function get( int $index ): RepresentsFloat
	{
		$this->guardIndexExists( $index );

		return $this->floatTypes[ $index ];
	}

	public function set( int $index, RepresentsFloat $value ): static
	{
		$floatTypes           = $this->floatTypes;
		$floatTypes[ $index ] = $value;

		return new static( $floatTypes );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->floatTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( RepresentsFloat...$types ): static
	{
		$currentTypes = $this->floatTypes;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( RepresentsFloat...$types ): static
	{
		$currentTypes = $this->floatTypes;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsFloatTypeList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsFloatTypeList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( array_reverse( $this->floatTypes ) );
	}

	public function shuffle(): static
	{
		$types = $this->floatTypes;
		shuffle( $types );

		return new static( $types );
	}

	public function replace( int $index, RepresentsFloat $type ): static
	{
		$types           = $this->floatTypes;
		$types[ $index ] = $type;

		return new static( $types );
	}

	public function unique(): static
	{
		$uniqueTypes = new static();

		foreach ( $this->floatTypes as $type )
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
		$types = $this->floatTypes;
		usort(
			$types,
			function ( RepresentsFloat $firstType, RepresentsFloat $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);

		return new static( $types );
	}

	public function diff( RepresentsFloatTypeList $list ): RepresentsFloatTypeList
	{
		return new static(
			array_udiff(
				$this->floatTypes,
				$list,
				function ( RepresentsFloat $firstType, RepresentsFloat $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsFloatTypeList $list ): RepresentsFloatTypeList
	{
		return new static(
			array_uintersect(
				$this->floatTypes,
				$list,
				function ( RepresentsFloat $firstType, RepresentsFloat $secondType ): int
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
	 * @return \Iterator<int, RepresentsFloatTypeList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->floatTypes, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsFloatTypeList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->floatTypes, $index, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	private function existsIn( RepresentsFloat $typeToCheck, RepresentsList $list ): bool
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
			if ( !($type instanceof RepresentsFloat) || !static::isValueValid( $type ) )
			{
				throw (new InvalidFloatTypeListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $type, ] );
			}
		}
	}
}
