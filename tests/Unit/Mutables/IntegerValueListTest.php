<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidIntegerValueListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableIntegerValueList;
use ComponoKit\Types\Lists\Mutables\IntegerValueList;
use ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes\NoTwoIntegerValueList;
use PHPUnit\Framework\TestCase;

class IntegerValueListTest extends TestCase
{
	public static function EqualsDataProvider(): \Iterator
	{
		yield 'same order' => [
			new IntegerValueList( 1, 2 ),
			new IntegerValueList( 1, 2 ),
			true,
		];

		yield 'different order' => [
			new IntegerValueList( 1, 2 ),
			new IntegerValueList( 2, 1 ),
			false,
		];

		yield 'different count' => [
			new IntegerValueList( 1 ),
			new IntegerValueList( 1, 2 ),
			false,
		];

		yield 'unequal types' => [
			new IntegerValueList( 1 ),
			new NoTwoIntegerValueList( 1 ),
			false,
		];
	}

	/**
	 * @dataProvider EqualsDataProvider
	 */
	public function testEquals( RepresentsMutableIntegerValueList $originalList, RepresentsMutableIntegerValueList $actualList, bool $expected ): void
	{
		self::assertSame( $expected, $originalList->equals( $actualList ) );
	}

	public static function EqualsValuesDataProvider(): \Iterator
	{
		yield 'same values same order' => [
			[ 1, 2 ],
			[ 1, 2 ],
			true,
		];

		yield 'same values different order' => [
			[ 1, 2 ],
			[ 2, 1 ],
			false,
		];

		yield 'different values' => [
			[ 1 ],
			[ 2 ],
			false,
		];

		yield 'different count' => [
			[ 1 ],
			[ 1, 1 ],
			false,
		];
	}

	/**
	 * @dataProvider EqualsValuesDataProvider
	 */
	public function testEqualsValues( array $values1, array $values2, bool $expected ): void
	{
		$originalList = new IntegerValueList( ...$values1 );
		$actualList   = new IntegerValueList( ...$values2 );

		self::assertSame( $expected, $originalList->equalsValues( $actualList ) );
	}

	public function testRemove(): void
	{
		$list = new IntegerValueList( 1, 2 );
		$list->remove( 0 );

		self::assertCount( 1, $list );
	}

	public function testAppend(): void
	{
		$list = new IntegerValueList( 1, 2 );
		$list->append( 1 );

		self::assertCount( 3, $list );

		self::assertSame( 1, $list->get( 0 ) );
		self::assertSame( 2, $list->get( 1 ) );
		self::assertSame( 1, $list->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$list = new IntegerValueList( 1, 2 );
		$list->prepend( 1 );

		self::assertCount( 3, $list );

		self::assertSame( 1, $list->get( 0 ) );
		self::assertSame( 1, $list->get( 1 ) );
		self::assertSame( 2, $list->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$list = new IntegerValueList( 1, 2 );
		$list->appendList( new IntegerValueList( 1 ) );

		self::assertCount( 3, $list );

		self::assertSame( 1, $list->get( 0 ) );
		self::assertSame( 2, $list->get( 1 ) );
		self::assertSame( 1, $list->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$list = new IntegerValueList( 1, 2 );
		$list->prependList( new IntegerValueList( 1 ) );

		self::assertCount( 3, $list );

		self::assertSame( 1, $list->get( 0 ) );
		self::assertSame( 1, $list->get( 1 ) );
		self::assertSame( 2, $list->get( 2 ) );
	}

	public function testUnique(): void
	{
		$list = new IntegerValueList( 1, 2, 1 );
		$list->unique();

		self::assertCount( 2, $list );
		self::assertSame( 1, $list->get( 0 ) );
		self::assertSame( 2, $list->get( 1 ) );
	}

	public function testDiff(): void
	{
		$list1 = new IntegerValueList( 1, 2 );
		$list2 = $list1->diff( new IntegerValueList( 2 ) );

		self::assertCount( 1, $list2 );
		self::assertSame( 1, $list2->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$list1 = new IntegerValueList( 1, 2 );
		$list2 = $list1->intersect( new IntegerValueList( 2 ) );

		self::assertCount( 1, $list2 );
		self::assertSame( 2, $list2->get( 0 ) );
	}

	public function testSplit(): void
	{
		$list        = new IntegerValueList( 1, 2, 3 );
		$chunkedList = iterator_to_array( $list->split( 2 ) );

		self::assertCount( 3, $list );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new IntegerValueList( 1, 2 ), $chunkedList[0] );
		self::assertEquals( new IntegerValueList( 3 ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$list = new IntegerValueList( 1, 2, 3 );
		$list->slice( 1, 1 );

		self::assertCount( 1, $list );
		self::assertEquals( new IntegerValueList( 2 ), $list );
	}

	public function testShuffle(): void
	{
		$list = new IntegerValueList( 1, 2 );
		$list->shuffle();

		self::assertCount( 2, $list );
		self::assertEqualsCanonicalizing( new IntegerValueList( 1, 2 ), $list );
	}

	public function testSort(): void
	{
		$list = new IntegerValueList( 3, 1 );

		$comparer = new class implements ComparesValues {
			public function compare( int|float|string|bool|array $firstValue, int|float|string|bool|array $secondValue ): int
			{
				return $firstValue <=> $secondValue;
			}
		};

		$list->sort( $comparer );

		self::assertEquals( new IntegerValueList( 1, 3 ), $list );
	}

	public function testReverse(): void
	{
		$list = new IntegerValueList( 1, 2, 3 );
		$list->reverse();

		self::assertEquals( new IntegerValueList( 3, 2, 1 ), $list );
	}

	public static function ReplaceDataProvider(): \Iterator
	{
		yield 'replace first' => [
			[ 2, 1 ],
			0,
			1,
		];

		yield 'replace second' => [
			[ 1, 2 ],
			1,
			1,
		];
	}

	/**
	 * @dataProvider ReplaceDataProvider
	 */
	public function testReplace( array $values, int $index, int $expected ): void
	{
		$list = new IntegerValueList( ...$values );
		$list->replace( $index, $expected );

		self::assertSame( $expected, $list->get( $index ) );
	}

	public function testReplaceThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new IntegerValueList( 1 );
		$list->replace( 99, 2 );
	}

	public static function ExistsDataProvider(): \Iterator
	{
		yield 'index exists' => [
			[ 1 ],
			0,
			true,
		];

		yield 'index does not exist' => [
			[ 1 ],
			1,
			false,
		];
	}

	/**
	 * @dataProvider ExistsDataProvider
	 */
	public function testExists( array $values, int $index, bool $expected ): void
	{
		$list = new IntegerValueList( ...$values );

		self::assertSame( $expected, $list->exists( $index ) );
	}

	public function testGet(): void
	{
		$list = new IntegerValueList( 1, 2, 3, 4 );

		self::assertSame( 4, $list->get( 3 ) );
	}

	public function testGetThrowsExceptionIfIndexNotExists(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new IntegerValueList( 1 );
		$list->get( 42 );
	}

	public function testFirst(): void
	{
		$list = new IntegerValueList( 1, 2 );

		self::assertSame( 1, $list->first() );
	}

	public function testLast(): void
	{
		$list = new IntegerValueList( 1, 2 );

		self::assertSame( 2, $list->last() );
	}

	public function testFindIndex(): void
	{
		$list = new IntegerValueList( 1, 2 );

		$matcher = new class implements MatchesValue {
			public function matches( int|float|string|bool|array $value ): bool
			{
				return $value === 2;
			}
		};

		self::assertSame( 1, $list->findIndex( $matcher ) );
	}

	public function testFindIndexReturnsNullIfNothingMatched(): void
	{
		$list = new IntegerValueList( 1 );

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
		$list = new IntegerValueList( 1, 2 );

		$json    = $list->toJson();
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 1, 2 ], $decoded );
	}

	public function testToJsonPretty(): void
	{
		$list = new IntegerValueList( 1, 2 );

		$json = $list->toJson( true );

		self::assertStringContainsString( "\n", $json );
		self::assertSame( [ 1, 2 ], json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function testJsonSerialize(): void
	{
		$list = new IntegerValueList( 1, 2 );

		$json    = json_encode( $list );
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 1, 2 ], $decoded );
	}

	public function testFromJson(): void
	{
		$list         = IntegerValueList::fromJson( '[1, 2]' );
		$expectedList = new IntegerValueList( 1, 2 );

		self::assertEquals( $expectedList, $list );
	}

	public function testThrowsInvalidValueExceptionIfValueInvalid(): void
	{
		$this->expectExceptionObject(
			new InvalidIntegerValueListException( 'Invalid ' . NoTwoIntegerValueList::class )
		);

		new NoTwoIntegerValueList( 2 );
	}
}
