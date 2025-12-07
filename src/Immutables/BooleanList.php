<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableBooleanList;
use ComponoKit\Types\Lists\Immutables\Traits\RepresentingImmutableBooleanList;
use ComponoKit\Types\Lists\Exceptions\InvalidBooleanListException;

class BooleanList implements RepresentsImmutableBooleanList
{
	use RepresentingImmutableBooleanList;

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
