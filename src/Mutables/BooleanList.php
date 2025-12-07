<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Exceptions\InvalidBooleanListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableBooleanList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableBooleanList;

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
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !($type instanceof RepresentsBoolean) )
			{
				throw (new InvalidBooleanListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
