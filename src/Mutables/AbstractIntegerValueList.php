<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidIntegerValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableIntegerValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableIntegerValueList;

abstract class AbstractIntegerValueList implements RepresentsMutableIntegerValueList
{
	use RepresentingMutableIntegerValueList;

	public function __construct( int ...$integers )
	{
		$this->validate( ...$integers );

		$this->integers = $integers;
	}

	abstract protected static function isValueValid( int $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function diff( RepresentsIntegerValueList $list ): RepresentsIntegerValueList
	{
		return new static(
			...array_udiff(
				$this->integers,
				$list->toNativeType(),
				function ( int $firstValue, int $secondValue ): int
				{
					return $firstValue === $secondValue ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsIntegerValueList $list ): RepresentsIntegerValueList
	{
		return new static(
			...array_uintersect(
				$this->integers,
				$list->toNativeType(),
				function ( int $firstValue, int $secondValue ): int
				{
					return $firstValue === $secondValue ? 0 : 1;
				}
			)
		);
	}

	/**
	 * @param int  $count
	 *
	 * @return \Iterator<int, RepresentsIntegerValueList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->integers, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->integers = array_slice( $this->integers, $index, $count );
	}

	protected function validate( int ...$values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidIntegerValueListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
