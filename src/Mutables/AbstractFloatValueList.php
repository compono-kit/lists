<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidFloatValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableFloatValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableFloatValueList;

abstract class AbstractFloatValueList implements RepresentsMutableFloatValueList
{
	use RepresentingMutableFloatValueList;

	/**
	 * @param float[] $floats
	 */
	public function __construct( private array $floats = [] )
	{
		$this->validate( $floats );
	}

	abstract protected static function isValueValid( float $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function diff( RepresentsFloatValueList $list ): RepresentsFloatValueList
	{
		return new static(
			array_udiff(
				$this->floats,
				$list,
				function ( float $firstValue, float $secondValue ): int
				{
					return $firstValue === $secondValue ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsFloatValueList $list ): RepresentsFloatValueList
	{
		return new static(
			array_uintersect(
				$this->floats,
				$list,
				function ( float $firstValue, float $secondValue ): int
				{
					return $firstValue === $secondValue ? 1 : 0;
				}
			),
		);
	}

	/**
	 * @param int  $count
	 * @param bool $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsFloatValueList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->floats, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsFloatValueList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->floats, $index, $count, $preserveIndices ) as $values )
		{
			yield new static( $values );
		}
	}

	protected function validate( array $values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || !is_float( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidFloatValueListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
