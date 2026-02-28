<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidMixedValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableMixedValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableMixedValueList;

abstract class AbstractMixedValueList implements RepresentsMutableMixedValueList
{
	use RepresentingMutableMixedValueList;

	public function __construct( int|float|string|bool|array ...$mixedValues )
	{
		$this->validate( $mixedValues );

		$this->mixedValues = $mixedValues;
	}

	abstract protected static function isValueValid( string $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function diff( RepresentsMixedValueList $list ): RepresentsMixedValueList
	{
		return new static(
			...array_udiff(
				$this->mixedValues,
				$list,
				function ( int|float|string|bool|array $firstValue, int|float|string|bool|array $secondValue ): int
				{
					return $firstValue === $secondValue ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsMixedValueList $list ): RepresentsMixedValueList
	{
		return new static(
			...array_uintersect(
				$this->mixedValues,
				$list,
				function ( int|float|string|bool|array $firstValue, int|float|string|bool|array $secondValue ): int
				{
					return $firstValue === $secondValue ? 1 : 0;
				}
			)
		);
	}

	/**
	 * @param int $count
	 *
	 * @return \Iterator<int, RepresentsMixedValueList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->mixedValues, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->mixedValues = array_slice( $this->mixedValues, $index, $count );
	}

	protected function validate( array $values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || (!is_scalar( $value ) && !is_array( $value )) || !static::isValueValid( $value ) )
			{
				throw (new InvalidMixedValueListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
