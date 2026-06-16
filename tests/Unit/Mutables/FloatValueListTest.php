<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidFloatValueListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableFloatValueList;
use ComponoKit\Types\Lists\Mutables\FloatValueList;
use ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes\NoTwoFloatValueList;
use PHPUnit\Framework\TestCase;

class FloatValueListTest extends TestCase
{
	public static function EqualsDataProvider(): \Iterator
	{
		yield 'same order' => [
			new FloatValueList( 1.1, 2.2 ),
			new FloatValueList( 1.1, 2.2 ),
			true,
		];

		yield 'different order' => [
			new FloatValueList( 1.1, 2.2 ),
			new FloatValueList( 2.2, 1.1 ),
			false,
		];

		yield 'different count' => [
			new FloatValueList( 1.1 ),
			new FloatValueList( 1.1, 2.2 ),
			false,
		];

		yield 'unequal types' => [
			new FloatValueList( 1.1 ),
			new NoTwoFloatValueList( 1.1 ),
			false,
		];
	}

	/**
	 * @dataProvider EqualsDataProvider
	 */
	public function testEquals( RepresentsMutableFloatValueList $originalList, RepresentsMutableFloatValueList $actualList, bool $expected ): void
	{
		self::assertSame( $expected, $originalList->equals( $actualList ) );
	}

	public static function EqualsValuesDataProvider(): \Iterator
	{
		yield 'same values same order' => [
			[ 1.1, 2.2 ],
			[ 1.1, 2.2 ],
			true,
		];

		yield 'same values different order' => [
			[ 1.1, 2.2 ],
			[ 2.2, 1.1 ],
			false,
		];

		yield 'different values' => [
			[ 1.1 ],
			[ 2.2 ],
			false,
		];

		yield 'different count' => [
			[ 1.1 ],
			[ 1.1, 1.1 ],
			false,
		];
	}

	/**
	 * @dataProvider EqualsValuesDataProvider
	 */
	public function testEqualsValues( array $values1, array $values2, bool $expected ): void
	{
		$originalList = new FloatValueList( ...$values1 );
		$actualList   = new FloatValueList( ...$values2 );

		self::assertSame( $expected, $originalList->equalsValues( $actualList ) );
	}

	public function testRemove(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );
		$list->remove( 0 );

		self::assertCount( 1, $list );
	}

	public function testAppend(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );
		$list->append( 1.1 );

		self::assertCount( 3, $list );

		self::assertSame( 1.1, $list->get( 0 ) );
		self::assertSame( 2.2, $list->get( 1 ) );
		self::assertSame( 1.1, $list->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );
		$list->prepend( 1.1 );

		self::assertCount( 3, $list );

		self::assertSame( 1.1, $list->get( 0 ) );
		self::assertSame( 1.1, $list->get( 1 ) );
		self::assertSame( 2.2, $list->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );
		$list->appendList( new FloatValueList( 1.1 ) );

		self::assertCount( 3, $list );

		self::assertSame( 1.1, $list->get( 0 ) );
		self::assertSame( 2.2, $list->get( 1 ) );
		self::assertSame( 1.1, $list->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );
		$list->prependList( new FloatValueList( 1.1 ) );

		self::assertCount( 3, $list );

		self::assertSame( 1.1, $list->get( 0 ) );
		self::assertSame( 1.1, $list->get( 1 ) );
		self::assertSame( 2.2, $list->get( 2 ) );
	}

	public function testUnique(): void
	{
		$list = new FloatValueList( 1.1, 2.2, 1.1 );
		$list->unique();

		self::assertCount( 2, $list );
		self::assertSame( 1.1, $list->get( 0 ) );
		self::assertSame( 2.2, $list->get( 1 ) );
	}

	public function testDiff(): void
	{
		$list1 = new FloatValueList( 1.1, 2.2 );
		$list2 = $list1->diff( new FloatValueList( 2.2 ) );

		self::assertCount( 1, $list2 );
		self::assertSame( 1.1, $list2->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$list1 = new FloatValueList( 1.1, 2.2 );
		$list2 = $list1->intersect( new FloatValueList( 2.2 ) );

		self::assertCount( 1, $list2 );
		self::assertSame( 2.2, $list2->get( 0 ) );
	}

	public function testSplit(): void
	{
		$list        = new FloatValueList( 1.1, 2.2, 3.3 );
		$chunkedList = iterator_to_array( $list->split( 2 ) );

		self::assertCount( 3, $list );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new FloatValueList( 1.1, 2.2 ), $chunkedList[0] );
		self::assertEquals( new FloatValueList( 3.3 ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$list = new FloatValueList( 1.1, 2.2, 3.3 );
		$list->slice( 1, 1 );

		self::assertCount( 1, $list );
		self::assertEquals( new FloatValueList( 2.2 ), $list );
	}

	public function testShuffle(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );
		$list->shuffle();

		self::assertCount( 2, $list );
		self::assertEqualsCanonicalizing( new FloatValueList( 1.1, 2.2 ), $list );
	}

	public function testSort(): void
	{
		$list = new FloatValueList( 3.3, 1.1 );

		$comparer = new class implements ComparesValues {
			public function compare( int|float|string|bool|array $firstValue, int|float|string|bool|array $secondValue ): int
			{
				return $firstValue <=> $secondValue;
			}
		};

		$list->sort( $comparer );

		self::assertEquals( new FloatValueList( 1.1, 3.3 ), $list );
	}

	public function testReverse(): void
	{
		$list = new FloatValueList( 1.1, 2.2, 3.3 );
		$list->reverse();

		self::assertEquals( new FloatValueList( 3.3, 2.2, 1.1 ), $list );
	}

	public static function ReplaceDataProvider(): \Iterator
	{
		yield 'replace first' => [
			[ 2.2, 1.1 ],
			0,
			1.1,
		];

		yield 'replace second' => [
			[ 1.1, 2.2 ],
			1,
			1.1,
		];
	}

	/**
	 * @dataProvider ReplaceDataProvider
	 */
	public function testReplace( array $values, int $index, float $expected ): void
	{
		$list = new FloatValueList( ...$values );
		$list->replace( $index, $expected );

		self::assertSame( $expected, $list->get( $index ) );
	}

	public function testReplaceThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new FloatValueList( 1.1 );
		$list->replace( 99, 2.2 );
	}

	public static function ExistsDataProvider(): \Iterator
	{
		yield 'index exists' => [
			[ 1.1 ],
			0,
			true,
		];

		yield 'index does not exist' => [
			[ 1.1 ],
			1,
			false,
		];
	}

	/**
	 * @dataProvider ExistsDataProvider
	 */
	public function testExists( array $values, int $index, bool $expected ): void
	{
		$list = new FloatValueList( ...$values );

		self::assertSame( $expected, $list->exists( $index ) );
	}

	public function testGet(): void
	{
		$list = new FloatValueList( 1.1, 2.2, 3.3, 4.4 );

		self::assertSame( 4.4, $list->get( 3 ) );
	}

	public function testGetThrowsExceptionIfIndexNotExists(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new FloatValueList( 1.1 );
		$list->get( 42 );
	}

	public function testFirst(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );

		self::assertSame( 1.1, $list->first() );
	}

	public function testLast(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );

		self::assertSame( 2.2, $list->last() );
	}

	public function testFindIndex(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );

		$matcher = new class implements MatchesValue {
			public function matches( int|float|string|bool|array $value ): bool
			{
				return $value === 2.2;
			}
		};

		self::assertSame( 1, $list->findIndex( $matcher ) );
	}

	public function testFindIndexReturnsNullIfNothingMatched(): void
	{
		$list = new FloatValueList( 1.1 );

		$matcher = new class implements MatchesValue {
			public function matches( int|float|string|bool|array $value ): bool
			{
				return false;
			}
		};

		self::assertNull( $list->findIndex( $matcher ) );
	}

	public function testToJson(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );

		$json    = $list->toJson();
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 1.1, 2.2 ], $decoded );
	}

	public function testToJsonPretty(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );

		$json = $list->toJson( true );

		self::assertStringContainsString( "\n", $json );
		self::assertSame( [ 1.1, 2.2 ], json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function testJsonSerialize(): void
	{
		$list = new FloatValueList( 1.1, 2.2 );

		$json    = json_encode( $list );
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 1.1, 2.2 ], $decoded );
	}

	public function testFromJson(): void
	{
		$list         = FloatValueList::fromJson( '[1.1, 2.2]' );
		$expectedList = new FloatValueList( 1.1, 2.2 );

		self::assertEquals( $expectedList, $list );
	}

	public function testThrowsInvalidValueExceptionIfValueInvalid(): void
	{
		$this->expectExceptionObject(
			new InvalidFloatValueListException( 'Invalid ' . NoTwoFloatValueList::class )
		);

		new NoTwoFloatValueList( 2.2 );
	}
}
