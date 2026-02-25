<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidIntegerValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableIntegerValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableIntegerValueList;

abstract class AbstractIntegerValueList implements RepresentsMutableIntegerValueList
{
	use RepresentingMutableIntegerValueList;

	/**
	 * @param int[] $integers
	 */
	public function __construct( private array $integers = [] )
	{
		$this->validate( $integers );
	}

	abstract protected static function isValueValid( int $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function diff( RepresentsIntegerValueList $list ): RepresentsIntegerValueList
	{
		return new static(
			array_udiff(
				$this->integers,
				$list,
				function ( int $firstValue, int $secondValue ): int
				{
					return $firstValue === $secondValue ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsIntegerValueList $list ): RepresentsIntegerValueList
	{
		return new static(
			array_uintersect(
				$this->integers,
				$list,
				function ( int $firstValue, int $secondValue ): int
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
	 * @return \Iterator<int, RepresentsIntegerValueList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->integers, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsIntegerValueList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->integers, $index, $count, $preserveIndices ) as $values )
		{
			yield new static( $values );
		}
	}

	protected function validate( array $values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || !is_int( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidIntegerValueListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
