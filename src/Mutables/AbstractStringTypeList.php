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

	public function __construct( RepresentsString ...$stringTypes )
	{
		$this->validate( $stringTypes );

		$this->stringTypes = $stringTypes;
	}

	abstract protected static function isValueValid( RepresentsString $type ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function diff( RepresentsStringTypeList $list ): RepresentsStringTypeList
	{
		return new static(
			...array_udiff(
				$this->stringTypes,
				$list,
				function ( RepresentsString $firstType, RepresentsString $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsStringTypeList $list ): RepresentsStringTypeList
	{
		return new static(
			...array_uintersect(
				$this->stringTypes,
				$list,
				function ( RepresentsString $firstType, RepresentsString $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 1 : 0;
				}
			)
		);
	}

	/**
	 * @param int $count
	 *
	 * @return \Iterator<int, RepresentsStringTypeList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->stringTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->stringTypes = array_slice( $this->stringTypes, $index, $count );
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
