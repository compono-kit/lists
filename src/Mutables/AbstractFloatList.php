<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Lists\Exceptions\InvalidFloatListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableFloatList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableFloatList;

abstract class AbstractFloatList implements RepresentsMutableFloatList
{
	use RepresentingMutableFloatList;

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
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !($type instanceof RepresentsFloat) || !static::isValueValid( $type ) )
			{
				throw (new InvalidFloatListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
