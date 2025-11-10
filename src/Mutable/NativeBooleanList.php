<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable;

use ComponoKit\Types\Lists\Interfaces\Mutable\RepresentsMutableNativeBooleanList;
use ComponoKit\Types\Lists\Mutable\Traits\RepresentingMutableNativeBooleanList;
use ComponoKit\Types\Lists\Exceptions\InvalidBooleanListException;

class NativeBooleanList implements RepresentsMutableNativeBooleanList
{
	use RepresentingMutableNativeBooleanList;

	/**
	 * @param bool[] $booleans
	 */
	public function __construct( private array $booleans )
	{
		$this->validate( $booleans );
	}

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
			if ( !is_bool( $value ) )
			{
				throw (new InvalidBooleanListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $value, ] );
			}
		}
	}
}
