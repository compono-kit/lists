<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableFloatList;
use ComponoKit\Types\Lists\Immutables\Traits\RepresentingImmutableFloatList;
use ComponoKit\Types\Lists\Exceptions\InvalidFloatListException;

abstract class AbstractFloatList implements RepresentsImmutableFloatList
{
	use RepresentingImmutableFloatList;

	/**
	 * @param RepresentsFloat[] $floatTypes
	 */
	public function __construct( private array $floatTypes )
	{
		$this->validate( $floatTypes );
	}

	abstract protected static function isValueValid( RepresentsFloat $type ): bool;

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
			if ( !($type instanceof RepresentsFloat) || !static::isValueValid( $type ) )
			{
				throw (new InvalidFloatListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $type, ] );
			}
		}
	}
}
