<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Traits\RepresentingStringValueList;

trait RepresentingMutableStringValueList
{
	use RepresentingStringValueList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->strings, $index, 1 );
	}

	public function reverse(): void
	{
		$this->strings = array_reverse( $this->strings );
	}

	public function shuffle(): void
	{
		shuffle( $this->strings );
	}

	public function unique(): void
	{
		$uniqueValues = [];

		foreach ( $this->strings as $value )
		{
			foreach ( $uniqueValues as $uniqueValue )
			{
				if ( $uniqueValue === $value )
				{
					continue 2;
				}
			}

			$uniqueValues[] = $value;
		}

		$this->strings = $uniqueValues;
	}

	public function sort( ComparesValues $comparer ): void
	{
		usort( $this->strings, $comparer->compare( ... ) );
	}
}
