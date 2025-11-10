<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Interfaces\Mutable\RepresentsMutableBooleanList;
use ComponoKit\Types\Lists\Mutable\Traits\RepresentingMutableBooleanList;
use ComponoKit\Types\Lists\Exceptions\InvalidBooleanListException;

class BooleanList implements RepresentsMutableBooleanList
{
	use RepresentingMutableBooleanList;

	/**
	 * @param RepresentsBoolean[] $booleanTypes
	 */
	public function __construct( private array $booleanTypes )
	{
		$this->validate( $booleanTypes );
	}

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	protected function validate( array $types ): void
	{
		foreach ( $types as $type )
		{
			if ( !($type instanceof RepresentsBoolean) )
			{
				throw (new InvalidBooleanListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $type, ] );
			}
		}
	}
}
