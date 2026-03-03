<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Lists\Exceptions\InvalidFloatTypeListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableFloatTypeList;

abstract class AbstractFloatTypeList implements RepresentsMutableFloatTypeList
{
	use RepresentingMutableFloatTypeList;

	public function __construct( RepresentsFloat ...$floatTypes )
	{
		$this->validate( $floatTypes );

		$this->floatTypes = $floatTypes;
	}

	abstract protected static function isValueValid( RepresentsFloat $type ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function diff( RepresentsFloatTypeList $list ): RepresentsFloatTypeList
	{
		return new static(
			...array_udiff(
				$this->floatTypes,
				$list->toNativeType(),
				function ( RepresentsFloat $firstType, RepresentsFloat $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsFloatTypeList $list ): RepresentsFloatTypeList
	{
		return new static(
			...array_uintersect(
				$this->floatTypes,
				$list->toNativeType(),
				function ( RepresentsFloat $firstType, RepresentsFloat $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	/**
	 * @param int $count
	 *
	 * @return \Iterator<int, RepresentsFloatTypeList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->floatTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->floatTypes = array_slice( $this->floatTypes, $index, $count );
	}

	protected function validate( array $types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !($type instanceof RepresentsFloat) || !static::isValueValid( $type ) )
			{
				throw (new InvalidFloatTypeListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
