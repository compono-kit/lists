<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidNativeIntegerListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableNativeIntegerList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableNativeIntegerList;

abstract class AbstractNativeIntegerList implements RepresentsMutableNativeIntegerList
{
	use RepresentingMutableNativeIntegerList;

	/**
	 * @param int[] $integers
	 */
	public function __construct( private array $integers )
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

	protected function validate( array $values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || !is_int( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidNativeIntegerListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
