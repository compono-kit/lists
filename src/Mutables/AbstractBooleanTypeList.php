<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Exceptions\InvalidBooleanTypeListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableBooleanTypeList;

abstract class AbstractBooleanTypeList implements RepresentsMutableBooleanTypeList
{
	use RepresentingMutableBooleanTypeList;

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
		return new static( array_slice( $this->booleanTypes, $index, $count, $preserveIndices ) );
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
