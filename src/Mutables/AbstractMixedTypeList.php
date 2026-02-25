<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\InvalidMixedTypeListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableMixedTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableMixedTypeList;

abstract class AbstractMixedTypeList implements RepresentsMutableMixedTypeList
{
	use RepresentingMutableMixedTypeList;

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
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !($type instanceof RepresentsType) || !static::isValueValid( $type ) )
			{
				throw (new InvalidMixedTypeListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
