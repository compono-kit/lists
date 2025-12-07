<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidNativeMixedTypeListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableNativeMixedTypeList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableNativeMixedTypeList;

abstract class AbstractNativeMixedTypeList implements RepresentsMutableNativeMixedTypeList
{
	use RepresentingMutableNativeMixedTypeList;

	/**
	 * @param array<int, int|float|string|bool|array>[] $nativeMixedTypes
	 */
	public function __construct( private array $nativeMixedTypes )
	{
		$this->validate( $nativeMixedTypes );
	}

	abstract protected static function isValueValid( string $value ): bool;

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
			if ( !is_int( $index ) || (!is_scalar( $value ) && !is_array( $value )) || !static::isValueValid( $value ) )
			{
				throw (new InvalidNativeMixedTypeListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
