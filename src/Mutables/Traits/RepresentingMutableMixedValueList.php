<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Traits\RepresentingMixedValueList;

trait RepresentingMutableMixedValueList
{
	use RepresentingMixedValueList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->mixedValues, $index, 1 );
	}

	public function reverse(): void
	{
		$this->mixedValues = array_reverse( $this->mixedValues );
	}

	public function shuffle(): void
	{
		shuffle( $this->mixedValues );
	}

	public function unique(): void
	{
		$uniqueValues = [];

		foreach ( $this->mixedValues as $value )
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

		$this->mixedValues = $uniqueValues;
	}

	public function sort( ComparesValues $comparer ): void
	{
		usort( $this->mixedValues, [ $comparer, 'compare' ] );
	}
}
