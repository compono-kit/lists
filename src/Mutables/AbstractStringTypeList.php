<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Exceptions\InvalidStringTypeListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableStringTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringTypeList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableStringTypeList;

abstract class AbstractStringTypeList implements RepresentsMutableStringTypeList
{
	use RepresentingMutableStringTypeList;

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
		return new static( array_slice( $this->stringTypes, $index, $count, $preserveIndices ) );
	}

	protected function validate( array $types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !($type instanceof RepresentsString) || !static::isValueValid( $type ) )
			{
				throw (new InvalidStringTypeListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
