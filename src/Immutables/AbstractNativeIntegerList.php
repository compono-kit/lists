<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableNativeIntegerList;
use ComponoKit\Types\Lists\Immutables\Traits\RepresentingImmutableNativeIntegerList;
use ComponoKit\Types\Lists\Exceptions\InvalidNativeIntegerListException;

abstract class AbstractNativeIntegerList implements RepresentsImmutableNativeIntegerList
{
	use RepresentingImmutableNativeIntegerList;

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
		foreach ( $values as $value )
		{
			if ( !is_int( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidNativeIntegerListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
