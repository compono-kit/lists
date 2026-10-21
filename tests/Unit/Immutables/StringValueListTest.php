<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidStringValueListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Immutables\StringValueList;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableStringValueList;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes\NoTwoStringValueList;
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
	public function testEquals( RepresentsImmutableStringValueList $originalList, RepresentsImmutableStringValueList $actualList, bool $expected ): void
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
		$originalList = new StringValueList( 'one', 'two' );
		$actualList   = $originalList->remove( 0 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 1, $actualList );
	}

	public function testAppend(): void
	{
		$originalList = new StringValueList( 'one', 'two' );
		$actualList   = $originalList->append( 'one' );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 'one', $actualList->get( 0 ) );
		self::assertSame( 'two', $actualList->get( 1 ) );
		self::assertSame( 'one', $actualList->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$originalList = new StringValueList( 'one', 'two' );
		$actualList   = $originalList->prepend( 'one' );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 'one', $actualList->get( 0 ) );
		self::assertSame( 'one', $actualList->get( 1 ) );
		self::assertSame( 'two', $actualList->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$originalList = new StringValueList( 'one', 'two' );
		$actualList   = $originalList->appendList( new StringValueList( 'one' ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 'one', $actualList->get( 0 ) );
		self::assertSame( 'two', $actualList->get( 1 ) );
		self::assertSame( 'one', $actualList->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$originalList = new StringValueList( 'one', 'two' );
		$actualList   = $originalList->prependList( new StringValueList( 'one' ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 'one', $actualList->get( 0 ) );
		self::assertSame( 'one', $actualList->get( 1 ) );
		self::assertSame( 'two', $actualList->get( 2 ) );
	}

	public function testUnique(): void
	{
		$originalList = new StringValueList( 'one', 'two', 'one' );
		$actualList   = $originalList->unique();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertSame( 'one', $originalList->get( 0 ) );
		self::assertSame( 'two', $originalList->get( 1 ) );
	}

	public function testDiff(): void
	{
		$originalList = new StringValueList( 'one', 'two' );
		$actualList   = $originalList->diff( new StringValueList( 'two' ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertSame( 'one', $actualList->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$originalList = new StringValueList( 'one', 'two' );
		$actualList   = $originalList->intersect( new StringValueList( 'two' ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertSame( 'two', $actualList->get( 0 ) );
	}

	public function testSplit(): void
	{
		$originalList = new StringValueList( 'one', 'two', 'three' );
		$chunkedList  = iterator_to_array( $originalList->split( 2 ) );

		self::assertCount( 3, $originalList );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new StringValueList( 'one', 'two' ), $chunkedList[0] );
		self::assertEquals( new StringValueList( 'three' ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$originalList = new StringValueList( 'one', 'two', 'three' );
		$actualList   = $originalList->slice( 1, 1 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new StringValueList( 'two' ), $actualList );
	}

	public function testShuffle(): void
	{
		$originalList = new StringValueList( 'one', 'two' );
		$actualList   = $originalList->shuffle();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertEqualsCanonicalizing( new StringValueList( 'one', 'two' ), $actualList );
	}

	public function testSort(): void
	{
		$originalList = new StringValueList( 'charlie', 'alpha' );

		$comparer = new class implements ComparesValues {
			public function compare( int|float|string|bool|array $firstValue, int|float|string|bool|array $secondValue ): int
			{
				return strcmp( (string)$firstValue, (string)$secondValue );
			}
		};

		$actualList = $originalList->sort( $comparer );

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals( new StringValueList( 'alpha', 'charlie' ), $actualList );
	}

	public function testReverse(): void
	{
		$originalList = new StringValueList( 'one', 'two', 'three' );
		$actualList   = $originalList->reverse();

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals( new StringValueList( 'three', 'two', 'one' ), $actualList );
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
		$originalList = new StringValueList( ...$values );
		$actualList   = $originalList->replace( $index, $expected );

		self::assertNotSame( $originalList, $actualList );
		self::assertSame( $expected, $actualList->get( $index ) );
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
			new InvalidStringValueListException( 'Invalid value in ' . NoTwoStringValueList::class )
		);

		new NoTwoStringValueList( 'two' );
	}

	public function testThrowsExceptionIfKeysAreUsed(): void
	{
		$this->expectExceptionObject(
			new InvalidStringValueListException( 'Invalid index in ' . StringValueList::class )
		);

		new StringValueList( ...[ 'foo' => 'bar' ] );
	}
}
