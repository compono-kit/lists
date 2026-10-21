<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanValueList;

trait RepresentingMutableBooleanValueList
{
	use RepresentingBooleanValueList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->booleans, $index, 1 );
	}

	public function reverse(): void
	{
		$this->booleans = array_reverse( $this->booleans );
	}

	public function shuffle(): void
	{
		shuffle( $this->booleans );
	}

	public function unique(): void
	{
		$uniqueValues = [];

		foreach ( $this->booleans as $value )
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

		$this->booleans = $uniqueValues;
	}

	public function sort( ComparesValues $comparer ): void
	{
		usort( $this->booleans, [ $comparer, 'compare' ] );
	}
}
