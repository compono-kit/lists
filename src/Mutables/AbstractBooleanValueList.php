<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidBooleanValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableBooleanValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableBooleanValueList;

abstract class AbstractBooleanValueList implements RepresentsMutableBooleanValueList
{
	use RepresentingMutableBooleanValueList;

	/**
	 * @param bool[] $booleans
	 */
	public function __construct( private array $booleans = [] )
	{
		$this->validate( $booleans );
	}

	abstract protected static function isValueValid( bool $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function diff( RepresentsBooleanValueList $list ): RepresentsBooleanValueList
	{
		return new static(
			array_udiff(
				$this->booleans,
				$list,
				function ( bool $firstValue, bool $secondValue ): int
				{
					return $firstValue === $secondValue ? 0 : 1;
				}
			),
		);
	}

	public function intersect( RepresentsBooleanValueList $list ): RepresentsBooleanValueList
	{
		return new static(
			array_uintersect(
				$this->booleans,
				$list,
				function ( bool $firstValue, bool $secondValue ): int
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
	 * @return \Iterator<int, RepresentsBooleanValueList>
	 */
	public function split( int $count, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_chunk( $this->booleans, $count, $preserveIndices ) as $types )
		{
			yield new static( $types );
		}
	}

	/**
	 * @param int      $index
	 * @param int|null $count
	 * @param bool     $preserveIndices
	 *
	 * @return \Iterator<int, RepresentsBooleanValueList>
	 */
	public function slice( int $index, ?int $count = null, bool $preserveIndices = false ): \Iterator
	{
		foreach ( array_slice( $this->booleans, $index, $count, $preserveIndices ) as $values )
		{
			yield new static( $values );
		}
	}

	protected function validate( array $values ): void
	{
		foreach ( $values as $value )
		{
			if ( !is_bool( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidBooleanValueListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
