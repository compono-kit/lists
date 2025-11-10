<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable;

use ComponoKit\Types\Lists\Interfaces\Mutable\RepresentsMutableNativeStringList;
use ComponoKit\Types\Lists\Mutable\Traits\RepresentingMutableNativeStringList;
use ComponoKit\Types\Lists\Exceptions\InvalidNativeStringListException;

abstract class AbstractNativeStringList implements RepresentsMutableNativeStringList
{
	use RepresentingMutableNativeStringList;

	/**
	 * @param string[] $strings
	 */
	public function __construct( private array $strings )
	{
		$this->validate( $strings );
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
		foreach ( $values as $value )
		{
			if ( !is_string( $value ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidNativeStringListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
