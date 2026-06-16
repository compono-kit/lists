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

	public function __construct( RepresentsBoolean ...$booleanTypes )
	{
		$this->validate( ...$booleanTypes );

		$this->booleanTypes = $booleanTypes;
	}

	abstract protected static function isValueValid( RepresentsBoolean $type ): bool;

	public function diff( RepresentsBooleanTypeList $list ): RepresentsBooleanTypeList
	{
		return new static(
			...array_udiff(
				$this->booleanTypes,
				$list->toNativeType(),
				function ( RepresentsBoolean $firstType, RepresentsBoolean $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsBooleanTypeList $list ): RepresentsBooleanTypeList
	{
		return new static(
			...array_uintersect(
				$this->booleanTypes,
				$list->toNativeType(),
				function ( RepresentsBoolean $firstType, RepresentsBoolean $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	/**
	 * @param int $count
	 *
	 * @return \Iterator<int, RepresentsBooleanTypeList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->booleanTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->booleanTypes = array_slice( $this->booleanTypes, $index, $count );
	}

	protected function validate( RepresentsBoolean ...$types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !static::isValueValid( $type ) )
			{
				throw (new InvalidBooleanTypeListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $type, ] );
			}
		}
	}
}
