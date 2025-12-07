<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableNativeMixedTypeList;
use ComponoKit\Types\Lists\Immutables\Traits\RepresentingImmutableNativeMixedTypeList;
use ComponoKit\Types\Lists\Exceptions\InvalidNativeMixedTypeListException;

abstract class AbstractNativeMixedTypeList implements RepresentsImmutableNativeMixedTypeList
{
	use RepresentingImmutableNativeMixedTypeList;

	/**
	 * @param array<int, int|float|string|bool|array> $nativeMixedTypes
	 */
	public function __construct( private array $nativeMixedTypes )
	{
		$this->validate( $nativeMixedTypes );
	}

	abstract protected static function isValueValid( int|float|string|bool|array $value ): bool;

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
			if ( (!is_scalar( $value ) && !is_array( $value )) || !static::isValueValid( $value ) )
			{
				throw (new InvalidNativeMixedTypeListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
