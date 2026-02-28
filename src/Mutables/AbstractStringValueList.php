<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidStringValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableStringValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableStringValueList;

abstract class AbstractStringValueList implements RepresentsMutableStringValueList
{
	use RepresentingMutableStringValueList;

	public function __construct( string ...$strings )
	{
		$this->validate( $strings );

		$this->strings = $strings;
	}

	abstract protected static function isValueValid( string $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function diff( RepresentsStringValueList $list ): RepresentsStringValueList
	{
		return new static(
			...array_udiff(
				$this->strings,
				$list,
				function ( string $firstValue, string $secondValue ): int
				{
					return $firstValue === $secondValue ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsStringValueList $list ): RepresentsStringValueList
	{
		return new static(
			...array_uintersect(
				$this->strings,
				$list,
				function ( string $firstValue, string $secondValue ): int
				{
					return $firstValue === $secondValue ? 1 : 0;
				}
			)
		);
	}

	/**
	 * @param int $count
	 *
	 * @return \Iterator<int, RepresentsStringValueList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->strings, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->strings = array_slice( $this->strings, $index, $count );
	}

	protected function validate( array $values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || !is_string( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidStringValueListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
