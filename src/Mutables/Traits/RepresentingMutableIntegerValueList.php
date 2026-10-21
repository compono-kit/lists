<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerValueList;

trait RepresentingMutableIntegerValueList
{
	use RepresentingIntegerValueList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->integers, $index, 1 );
	}

	public function reverse(): void
	{
		$this->integers = array_reverse( $this->integers );
	}

	public function shuffle(): void
	{
		shuffle( $this->integers );
	}

	public function unique(): void
	{
		$uniqueValues = [];

		foreach ( $this->integers as $value )
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

		$this->integers = $uniqueValues;
	}

	public function sort( ComparesValues $comparer ): void
	{
		usort( $this->integers, [ $comparer, 'compare' ] );
	}
}
