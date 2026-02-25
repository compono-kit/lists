<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Exceptions\InvalidStringTypeListException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableStringTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringTypeList;
use ComponoKit\Types\Lists\Traits\RepresentingStringList;

abstract class AbstractStringTypeList implements RepresentsImmutableStringTypeList
{
	use RepresentingStringList;

	/**
	 * @param RepresentsString[] $stringTypes
	 */
	public function __construct( private array $stringTypes = [] )
	{
		$this->validate( $stringTypes );
	}

	abstract protected static function isValueValid( RepresentsString $type ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->stringTypes[ $index ] );
	}

	public function set( int $index, RepresentsString $value ): static
	{
		$stringTypes           = $this->stringTypes;
		$stringTypes[ $index ] = $value;

		return new static( $stringTypes );
	}

	public function get( int $index ): RepresentsString
	{
		$this->guardIndexExists( $index );

		return $this->stringTypes[ $index ];
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$values = $this->stringTypes;
		unset( $values[ $index ] );

		return new static( $values );
	}

	public function append( RepresentsString...$types ): static
	{
		$currentTypes = $this->stringTypes;
		foreach ( $types as $type )
		{
			$currentTypes[] = $type;
		}

		return new static( $currentTypes );
	}

	public function prepend( RepresentsString...$types ): static
	{
		$currentTypes = $this->stringTypes;
		$currentTypes = $types + $currentTypes;

		return new static( $currentTypes );
	}

	public function appendList( RepresentsStringTypeList $list ): static
	{
		return new static( $this->toNativeType() + $list->toNativeType() );
	}

	public function prependList( RepresentsStringTypeList $list ): static
	{
		return new static( $list->toNativeType() + $this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( array_reverse( $this->stringTypes ) );
	}

	public function shuffle(): static
	{
		$types = $this->stringTypes;
		shuffle( $types );

		return new static( $types );
	}

	public function replace( int $index, RepresentsString $type ): static
	{
		$types           = $this->stringTypes;
		$types[ $index ] = $type;

		return new static( $types );
	}

	public function unique(): static
	{
		$uniqueTypes = new static();

		foreach ( $this->stringTypes as $type )
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
		$types = $this->stringTypes;
		usort(
			$types,
			function ( RepresentsString $firstType, RepresentsString $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);

		return new static( $types );
	}

	public function diff( RepresentsStringTypeList $list ): RepresentsStringTypeList
	{
		return new static(
			array_udiff(
				$this->stringTypes,
				$list,
				function ( RepresentsString $firstType, RepresentsString $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsStringTypeList $list ): RepresentsStringTypeList
	{
		return new static(
			array_uintersect(
				$this->stringTypes,
				$list,
				function ( RepresentsString $firstType, RepresentsString $secondType ): int
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
	 * @return \Iterator<int, RepresentsStringTypeList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->stringTypes, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsStringTypeList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->stringTypes, $index, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	protected function validate( array $types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) )
			{
				throw (new InvalidStringTypeListException( 'Invalid index in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}

			if ( !($type instanceof RepresentsString) || !static::isValueValid( $type ) )
			{
				throw (new InvalidStringTypeListException( 'Invalid value in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}

	private function existsIn( RepresentsString $typeToCheck, RepresentsList $list ): bool
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
