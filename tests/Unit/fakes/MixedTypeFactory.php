<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\fakes;

use ComponoKit\Types\Factories\StringFactory;
use ComponoKit\Types\Interfaces\Factories\BuildsTypes;
use ComponoKit\Types\Interfaces\RepresentsType;

class MixedTypeFactory implements BuildsTypes
{
	public function build( float|array|bool|int|string $value ): RepresentsType
	{
		if ( is_string( $value ) )
		{
			return (new StringFactory())->build( $value );
		}

		throw new \RuntimeException( 'Invalid type' );
	}
}
