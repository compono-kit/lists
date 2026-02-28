<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Exceptions\InvalidIntegerTypeListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableIntegerTypeList;

abstract class AbstractIntegerTypeList implements RepresentsMutableIntegerTypeList
{
	use RepresentingMutableIntegerTypeList;

	public function __construct( RepresentsInteger ...$integerTypes )
	{
		$this->validate( $integerTypes );

		$this->integerTypes = $integerTypes;
	}

	abstract protected static function isValueValid( RepresentsInteger $type ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function diff( RepresentsIntegerTypeList $list ): RepresentsIntegerTypeList
	{
		return new static(
			...array_udiff(
				$this->integerTypes,
				$list,
				function ( RepresentsInteger $firstType, RepresentsInteger $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsIntegerTypeList $list ): RepresentsIntegerTypeList
	{
		return new static(
			...array_uintersect(
				$this->integerTypes,
				$list,
				function ( RepresentsInteger $firstType, RepresentsInteger $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 1 : 0;
				}
			)
		);
	}

	/**
	 * @param int $count
	 *
	 * @return \Iterator<int, RepresentsIntegerTypeList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->integerTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->integerTypes = array_slice( $this->integerTypes, $index, $count );
	}

	protected function validate( array $types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !($type instanceof RepresentsInteger) || !static::isValueValid( $type ) )
			{
				throw (new InvalidIntegerTypeListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
