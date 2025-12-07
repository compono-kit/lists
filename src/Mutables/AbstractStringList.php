<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Exceptions\InvalidStringListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableStringList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableStringList;

abstract class AbstractStringList implements RepresentsMutableStringList
{
	use RepresentingMutableStringList;

	/**
	 * @param RepresentsString[] $stringTypes
	 */
	public function __construct( private array $stringTypes )
	{
		$this->validate( $stringTypes );
	}

	abstract protected static function isValueValid( RepresentsString $type ): bool;

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
			if ( !is_int( $index ) || !($type instanceof RepresentsString) || !static::isValueValid( $type ) )
			{
				throw (new InvalidStringListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
