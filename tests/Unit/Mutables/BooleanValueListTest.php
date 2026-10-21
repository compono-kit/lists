<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidBooleanValueListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableBooleanValueList;
use ComponoKit\Types\Lists\Mutables\BooleanValueList;
use ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes\OnlyTrueBooleanValueList;
use PHPUnit\Framework\TestCase;

class BooleanValueListTest extends TestCase
{
	public static function EqualsDataProvider(): \Iterator
	{
		yield 'same order' => [
			new BooleanValueList( true, false ),
			new BooleanValueList( true, false ),
			true,
		];

		yield 'different order' => [
			new BooleanValueList( true, false ),
			new BooleanValueList( false, true ),
			false,
		];

		yield 'different count' => [
			new BooleanValueList( true ),
			new BooleanValueList( true, false ),
			false,
		];

		yield 'unequal types' => [
			new BooleanValueList( true ),
			new OnlyTrueBooleanValueList( true ),
			false,
		];
	}

	/**
	 * @dataProvider EqualsDataProvider
	 */
	public function testEquals( RepresentsMutableBooleanValueList $originalList, RepresentsMutableBooleanValueList $actualList, bool $expected ): void
	{
		self::assertSame( $expected, $originalList->equals( $actualList ) );
	}

	public static function EqualsValuesDataProvider(): \Iterator
	{
		yield 'same values same order' => [
			[ true, false ],
			[ true, false ],
			true,
		];

		yield 'same values different order' => [
			[ true, false ],
			[ false, true ],
			false,
		];

		yield 'different values' => [
			[ true ],
			[ false ],
			false,
		];

		yield 'different count' => [
			[ true ],
			[ true, true ],
			false,
		];

		yield 'unexistent indices' => [
			[ 1 => false ],
			[ 0 => true, 1 => false ],
			false,
		];
	}

	/**
	 * @dataProvider EqualsValuesDataProvider
	 */
	public function testEqualsValues( array $values1, array $values2, bool $expected ): void
	{
		$originalList = new BooleanValueList( ...$values1 );
		$actualList   = new BooleanValueList( ...$values2 );

		self::assertSame( $expected, $originalList->equalsValues( $actualList ) );
	}

	public function testRemove(): void
	{
		$list = new BooleanValueList( true, false );
		$list->remove( 0 );

		self::assertCount( 1, $list );
	}

	public function testAppend(): void
	{
		$list = new BooleanValueList( true, false );
		$list->append( true );

		self::assertCount( 3, $list );

		self::assertSame( true, $list->get( 0 ) );
		self::assertSame( false, $list->get( 1 ) );
		self::assertSame( true, $list->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$list = new BooleanValueList( true, false );
		$list->prepend( true );

		self::assertCount( 3, $list );

		self::assertSame( true, $list->get( 0 ) );
		self::assertSame( true, $list->get( 1 ) );
		self::assertSame( false, $list->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$list = new BooleanValueList( true, false );
		$list->appendList( new BooleanValueList( true ) );

		self::assertCount( 3, $list );

		self::assertSame( true, $list->get( 0 ) );
		self::assertSame( false, $list->get( 1 ) );
		self::assertSame( true, $list->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$list = new BooleanValueList( true, false );
		$list->prependList( new BooleanValueList( true ) );

		self::assertCount( 3, $list );

		self::assertSame( true, $list->get( 0 ) );
		self::assertSame( true, $list->get( 1 ) );
		self::assertSame( false, $list->get( 2 ) );
	}

	public function testUnique(): void
	{
		$list = new BooleanValueList( true, false, true );
		$list->unique();

		self::assertCount( 2, $list );
		self::assertSame( true, $list->get( 0 ) );
		self::assertSame( false, $list->get( 1 ) );
	}

	public function testDiff(): void
	{
		$list1 = new BooleanValueList( true, false );
		$list2 = $list1->diff( new BooleanValueList( false ) );

		self::assertCount( 1, $list2 );
		self::assertSame( true, $list2->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$list1 = new BooleanValueList( true, false );
		$list2 = $list1->intersect( new BooleanValueList( false ) );

		self::assertCount( 1, $list2 );
		self::assertSame( false, $list2->get( 0 ) );
	}

	public function testSplit(): void
	{
		$list        = new BooleanValueList( true, false, true );
		$chunkedList = iterator_to_array( $list->split( 2 ) );

		self::assertCount( 3, $list );
		self::assertCount( 2, $chunkedList );
		self::assertCount( 2, $chunkedList[0] );
		self::assertCount( 1, $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$list = new BooleanValueList( true, false, true );
		$list->slice( 1, 1 );

		self::assertCount( 1, $list );
		self::assertEquals( new BooleanValueList( false ), $list );
	}

	public function testShuffle(): void
	{
		$list = new BooleanValueList( true, false );
		$list->shuffle();

		self::assertCount( 2, $list );

		$values = $list->toNativeType();
		sort( $values );

		self::assertSame( [ false, true ], $values );
	}

	public function testSort(): void
	{
		$list = new BooleanValueList( true, false );

		$comparer = new class implements ComparesValues {
			public function compare( int|float|string|bool|array $firstValue, int|float|string|bool|array $secondValue ): int
			{
				return $firstValue <=> $secondValue;
			}
		};

		$list->sort( $comparer );

		self::assertSame( [ false, true ], $list->toNativeType() );
	}

	public function testReverse(): void
	{
		$list = new BooleanValueList( true, false );
		$list->reverse();

		self::assertSame( [ false, true ], $list->toNativeType() );
	}

	public static function ReplaceDataProvider(): \Iterator
	{
		yield 'replace first' => [
			[ false, true ],
			0,
			true,
		];

		yield 'replace second' => [
			[ true, false ],
			1,
			true,
		];
	}

	/**
	 * @dataProvider ReplaceDataProvider
	 */
	public function testReplace( array $values, int $index, bool $expected ): void
	{
		$list = new BooleanValueList( ...$values );
		$list->replace( $index, $expected );

		self::assertSame( $expected, $list->get( $index ) );
	}

	public function testReplaceThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new BooleanValueList( true );
		$list->replace( 99, false );
	}

	public static function ExistsDataProvider(): \Iterator
	{
		yield 'index exists' => [
			[ true ],
			0,
			true,
		];

		yield 'index does not exist' => [
			[ true ],
			1,
			false,
		];
	}

	/**
	 * @dataProvider ExistsDataProvider
	 */
	public function testExists( array $values, int $index, bool $expected ): void
	{
		$list = new BooleanValueList( ...$values );

		self::assertSame( $expected, $list->exists( $index ) );
	}

	public function testGet(): void
	{
		$list = new BooleanValueList( true, false, true, false );

		self::assertSame( false, $list->get( 3 ) );
	}

	public function testGetThrowsExceptionIfIndexNotExists(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new BooleanValueList( true );
		$list->get( 42 );
	}

	public function testFirst(): void
	{
		$list = new BooleanValueList( true, false );

		self::assertSame( true, $list->first() );
	}

	public function testLast(): void
	{
		$list = new BooleanValueList( true, false );

		self::assertSame( false, $list->last() );
	}

	public function testFindIndex(): void
	{
		$list = new BooleanValueList( true, false );

		$matcher = new class implements MatchesValue {
			public function matches( int|float|string|bool|array $value ): bool
			{
				return $value === false;
			}
		};

		self::assertSame( 1, $list->findIndex( $matcher ) );
	}

	public function testFindIndexReturnsNullIfNothingMatched(): void
	{
		$list = new BooleanValueList( true );

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
		$list = new BooleanValueList( true, false );

		$json    = $list->toJson();
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ true, false ], $decoded );
	}

	public function testToJsonPretty(): void
	{
		$list = new BooleanValueList( true, false );

		$json = $list->toJson( true );

		self::assertStringContainsString( "\n", $json );
		self::assertSame( [ true, false ], json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function testJsonSerialize(): void
	{
		$list = new BooleanValueList( true, false );

		$json    = json_encode( $list );
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ true, false ], $decoded );
	}

	public function testFromJson(): void
	{
		$list         = BooleanValueList::fromJson( '[true, false]' );
		$expectedList = new BooleanValueList( true, false );

		self::assertEquals( $expectedList, $list );
	}

	public function testThrowsInvalidValueExceptionIfValueInvalid(): void
	{
		$this->expectExceptionObject(
			new InvalidBooleanValueListException( 'Invalid ' . OnlyTrueBooleanValueList::class )
		);

		new OnlyTrueBooleanValueList( false );
	}
}
