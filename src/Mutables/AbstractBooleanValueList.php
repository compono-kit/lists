<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidBooleanValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableBooleanValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableBooleanValueList;

abstract class AbstractBooleanValueList implements RepresentsMutableBooleanValueList
{
	use RepresentingMutableBooleanValueList;

	public function __construct( bool ...$booleans )
	{
		$this->validate( $booleans );

		$this->booleans = $booleans;
	}

	abstract protected static function isValueValid( bool $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function diff( RepresentsBooleanValueList $list ): RepresentsBooleanValueList
	{
		return new static(
			...array_udiff(
				$this->booleans,
				$list->toNativeType(),
				function ( bool $firstValue, bool $secondValue ): int
				{
					return $firstValue === $secondValue ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsBooleanValueList $list ): RepresentsBooleanValueList
	{
		return new static(
			...array_uintersect(
				$this->booleans,
				$list->toNativeType(),
				function ( bool $firstValue, bool $secondValue ): int
				{
					return $firstValue === $secondValue ? 0 : 1;
				}
			)
		);
	}

	/**
	 * @param int $count
	 *
	 * @return \Iterator<int, RepresentsBooleanValueList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->booleans, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->booleans = array_slice( $this->booleans, $index, $count );
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
