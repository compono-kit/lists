<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutable;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Interfaces\Mutable\RepresentsMutableMixedTypeList;
use ComponoKit\Types\Lists\Mutable\Traits\RepresentingMutableMixedTypeList;
use ComponoKit\Types\Lists\Exceptions\InvalidMixedTypeListException;

abstract class AbstractMixedTypeList implements RepresentsMutableMixedTypeList
{
	use RepresentingMutableMixedTypeList;

	/**
	 * @param RepresentsType[] $mixedTypes
	 */
	public function __construct( private array $mixedTypes )
	{
		$this->validate( $mixedTypes );
	}

	abstract protected static function isValueValid( RepresentsType $type ): bool;

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
			if ( !($type instanceof RepresentsType) || !static::isValueValid( $type ) )
			{
				throw (new InvalidMixedTypeListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $type, ] );
			}
		}
	}
}
