<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidStringValueListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableStringValueList;
use ComponoKit\Types\Lists\Mutables\StringValueList;
use ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes\NoTwoStringValueList;
use PHPUnit\Framework\TestCase;

class StringValueListTest extends TestCase
{
	public static function EqualsDataProvider(): \Iterator
	{
		yield 'same order' => [
			new StringValueList( 'one', 'two' ),
			new StringValueList( 'one', 'two' ),
			true,
		];

		yield 'different order' => [
			new StringValueList( 'one', 'two' ),
			new StringValueList( 'two', 'one' ),
			false,
		];

		yield 'different count' => [
			new StringValueList( 'one' ),
			new StringValueList( 'one', 'two' ),
			false,
		];

		yield 'unequal types' => [
			new StringValueList( 'one' ),
			new NoTwoStringValueList( 'one' ),
			false,
		];
	}

	/**
	 * @dataProvider EqualsDataProvider
	 */
	public function testEquals( RepresentsMutableStringValueList $originalList, RepresentsMutableStringValueList $actualList, bool $expected ): void
	{
		self::assertSame( $expected, $originalList->equals( $actualList ) );
	}

	public static function EqualsValuesDataProvider(): \Iterator
	{
		yield 'same values same order' => [
			[ 'one', 'two' ],
			[ 'one', 'two' ],
			true,
		];

		yield 'same values different order' => [
			[ 'one', 'two' ],
			[ 'two', 'one' ],
			false,
		];

		yield 'different values' => [
			[ 'one' ],
			[ 'two' ],
			false,
		];

		yield 'different count' => [
			[ 'one' ],
			[ 'one', 'one' ],
			false,
		];
	}

	/**
	 * @dataProvider EqualsValuesDataProvider
	 */
	public function testEqualsValues( array $values1, array $values2, bool $expected ): void
	{
		$originalList = new StringValueList( ...$values1 );
		$actualList   = new StringValueList( ...$values2 );

		self::assertSame( $expected, $originalList->equalsValues( $actualList ) );
	}

	public function testRemove(): void
	{
		$list = new StringValueList( 'one', 'two' );
		$list->remove( 0 );

		self::assertCount( 1, $list );
	}

	public function testAppend(): void
	{
		$list = new StringValueList( 'one', 'two' );
		$list->append( 'one' );

		self::assertCount( 3, $list );

		self::assertSame( 'one', $list->get( 0 ) );
		self::assertSame( 'two', $list->get( 1 ) );
		self::assertSame( 'one', $list->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$list = new StringValueList( 'one', 'two' );
		$list->prepend( 'one' );

		self::assertCount( 3, $list );

		self::assertSame( 'one', $list->get( 0 ) );
		self::assertSame( 'one', $list->get( 1 ) );
		self::assertSame( 'two', $list->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$list = new StringValueList( 'one', 'two' );
		$list->appendList( new StringValueList( 'one' ) );

		self::assertCount( 3, $list );

		self::assertSame( 'one', $list->get( 0 ) );
		self::assertSame( 'two', $list->get( 1 ) );
		self::assertSame( 'one', $list->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$list = new StringValueList( 'one', 'two' );
		$list->prependList( new StringValueList( 'one' ) );

		self::assertCount( 3, $list );

		self::assertSame( 'one', $list->get( 0 ) );
		self::assertSame( 'one', $list->get( 1 ) );
		self::assertSame( 'two', $list->get( 2 ) );
	}

	public function testUnique(): void
	{
		$list = new StringValueList( 'one', 'two', 'one' );
		$list->unique();

		self::assertCount( 2, $list );
		self::assertSame( 'one', $list->get( 0 ) );
		self::assertSame( 'two', $list->get( 1 ) );
	}

	public function testDiff(): void
	{
		$list1 = new StringValueList( 'one', 'two' );
		$list2 = $list1->diff( new StringValueList( 'two' ) );

		self::assertCount( 1, $list2 );
		self::assertSame( 'one', $list2->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$list1 = new StringValueList( 'one', 'two' );
		$list2 = $list1->intersect( new StringValueList( 'two' ) );

		self::assertCount( 1, $list2 );
		self::assertSame( 'two', $list2->get( 0 ) );
	}

	public function testSplit(): void
	{
		$list        = new StringValueList( 'one', 'two', 'three' );
		$chunkedList = iterator_to_array( $list->split( 2 ) );

		self::assertCount( 3, $list );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new StringValueList( 'one', 'two' ), $chunkedList[0] );
		self::assertEquals( new StringValueList( 'three' ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$list = new StringValueList( 'one', 'two', 'three' );
		$list->slice( 1, 1 );

		self::assertCount( 1, $list );
		self::assertEquals( new StringValueList( 'two' ), $list );
	}

	public function testShuffle(): void
	{
		$list = new StringValueList( 'one', 'two' );
		$list->shuffle();

		self::assertCount( 2, $list );
		self::assertEqualsCanonicalizing( new StringValueList( 'one', 'two' ), $list );
	}

	public function testSort(): void
	{
		$list = new StringValueList( 'charlie', 'alpha' );

		$comparer = new class implements ComparesValues {
			public function compare( int|float|string|bool|array $firstValue, int|float|string|bool|array $secondValue ): int
			{
				return strcmp( (string)$firstValue, (string)$secondValue );
			}
		};

		$list->sort( $comparer );

		self::assertEquals( new StringValueList( 'alpha', 'charlie' ), $list );
	}

	public function testReverse(): void
	{
		$list = new StringValueList( 'one', 'two', 'three' );
		$list->reverse();

		self::assertEquals( new StringValueList( 'three', 'two', 'one' ), $list );
	}

	public static function ReplaceDataProvider(): \Iterator
	{
		yield 'replace first' => [
			[ 'two', 'one' ],
			0,
			'one',
		];

		yield 'replace second' => [
			[ 'one', 'two' ],
			1,
			'one',
		];
	}

	/**
	 * @dataProvider ReplaceDataProvider
	 */
	public function testReplace( array $values, int $index, string $expected ): void
	{
		$list = new StringValueList( ...$values );
		$list->replace( $index, $expected );

		self::assertSame( $expected, $list->get( $index ) );
	}

	public function testReplaceThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new StringValueList( 'one' );
		$list->replace( 99, 'two' );
	}

	public static function ExistsDataProvider(): \Iterator
	{
		yield 'index exists' => [
			[ 'one' ],
			0,
			true,
		];

		yield 'index does not exist' => [
			[ 'one' ],
			1,
			false,
		];
	}

	/**
	 * @dataProvider ExistsDataProvider
	 */
	public function testExists( array $values, int $index, bool $expected ): void
	{
		$list = new StringValueList( ...$values );

		self::assertSame( $expected, $list->exists( $index ) );
	}

	public function testGet(): void
	{
		$list = new StringValueList( 'one', 'two', 'three', 'four' );

		self::assertSame( 'four', $list->get( 3 ) );
	}

	public function testGetThrowsExceptionIfIndexNotExists(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new StringValueList( 'one' );
		$list->get( 42 );
	}

	public function testFirst(): void
	{
		$list = new StringValueList( 'one', 'two' );

		self::assertSame( 'one', $list->first() );
	}

	public function testLast(): void
	{
		$list = new StringValueList( 'one', 'two' );

		self::assertSame( 'two', $list->last() );
	}

	public function testFindIndex(): void
	{
		$list = new StringValueList( 'one', 'two' );

		$matcher = new class implements MatchesValue {
			public function matches( int|float|string|bool|array $value ): bool
			{
				return $value === 'two';
			}
		};

		self::assertSame( 1, $list->findIndex( $matcher ) );
	}

	public function testFindIndexReturnsNullIfNothingMatched(): void
	{
		$list = new StringValueList( 'one' );

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
		$list = new StringValueList( 'one', 'two' );

		$json    = $list->toJson();
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 'one', 'two' ], $decoded );
	}

	public function testToJsonPretty(): void
	{
		$list = new StringValueList( 'one', 'two' );

		$json = $list->toJson( true );

		self::assertStringContainsString( "\n", $json );
		self::assertSame( [ 'one', 'two' ], json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function testJsonSerialize(): void
	{
		$list = new StringValueList( 'one', 'two' );

		$json    = json_encode( $list );
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 'one', 'two' ], $decoded );
	}

	public function testFromJson(): void
	{
		$list         = StringValueList::fromJson( '["one", "two"]' );
		$expectedList = new StringValueList( 'one', 'two' );

		self::assertEquals( $expectedList, $list );
	}

	public function testThrowsInvalidValueExceptionIfValueInvalid(): void
	{
		$this->expectExceptionObject(
			new InvalidStringValueListException( 'Invalid ' . NoTwoStringValueList::class )
		);

		new NoTwoStringValueList( 'two' );
	}
}
