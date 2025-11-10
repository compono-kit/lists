<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutable;

use ComponoKit\Types\Lists\Interfaces\Immutable\RepresentsImmutableNativeFloatList;
use ComponoKit\Types\Lists\Immutable\Traits\RepresentingImmutableNativeFloatList;
use ComponoKit\Types\Lists\Exceptions\InvalidNativeFloatListException;

abstract class AbstractNativeFloatList implements RepresentsImmutableNativeFloatList
{
	use RepresentingImmutableNativeFloatList;

	/**
	 * @param float[] $floats
	 */
	public function __construct( private array $floats )
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

	protected function validate( array $values ): void
	{
		foreach ( $values as $value )
		{
			if ( !is_float( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidNativeFloatListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
