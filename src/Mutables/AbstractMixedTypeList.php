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

	public function __construct( RepresentsType ...$mixedTypes )
	{
		$this->validate( ...$mixedTypes );

		$this->mixedTypes = $mixedTypes;
	}

	abstract protected static function isValueValid( RepresentsType $type ): bool;

	public function diff( RepresentsMixedTypeList $list ): RepresentsMixedTypeList
	{
		return new static(
			...array_udiff(
				$this->mixedTypes,
				$list->toNativeType(),
				function ( RepresentsType $firstType, RepresentsType $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsMixedTypeList $list ): RepresentsMixedTypeList
	{
		return new static(
			...array_uintersect(
				$this->mixedTypes,
				$list->toNativeType(),
				function ( RepresentsType $firstType, RepresentsType $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	/**
	 * @param int $count
	 *
	 * @return \Iterator<int, RepresentsMixedTypeList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->mixedTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->mixedTypes = array_slice( $this->mixedTypes, $index, $count );
	}

	protected function validate( RepresentsType ...$types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !static::isValueValid( $type ) )
			{
				throw (new InvalidMixedTypeListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
