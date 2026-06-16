<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidFloatValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableFloatValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableFloatValueList;

abstract class AbstractFloatValueList implements RepresentsMutableFloatValueList
{
	use RepresentingMutableFloatValueList;

	public function __construct( float ...$floats )
	{
		$this->validate( ...$floats );

		$this->floats = $floats;
	}

	abstract protected static function isValueValid( float $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function diff( RepresentsFloatValueList $list ): RepresentsFloatValueList
	{
		return new static(
			...array_udiff(
				$this->floats,
				$list->toNativeType(),
				function ( float $firstValue, float $secondValue ): int
				{
					return $firstValue === $secondValue ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsFloatValueList $list ): RepresentsFloatValueList
	{
		return new static(
			...array_uintersect(
				$this->floats,
				$list->toNativeType(),
				function ( float $firstValue, float $secondValue ): int
				{
					return $firstValue === $secondValue ? 0 : 1;
				}
			)
		);
	}

	/**
	 * @param int $count
	 *
	 * @return \Iterator<int, RepresentsFloatValueList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->floats, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->floats = array_slice( $this->floats, $index, $count );
	}

	protected function validate( float ...$values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidFloatValueListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
