<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidMixedValueListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Immutables\MixedValueList;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableMixedValueList;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes\NoIntegerMixedValueList;
use PHPUnit\Framework\TestCase;

class MixedValueListTest extends TestCase
{
	public static function EqualsDataProvider(): \Iterator
	{
		yield 'same order' => [
			new MixedValueList( 'test-1', 42, 1.1, true ),
			new MixedValueList( 'test-1', 42, 1.1, true ),
			true,
		];

		yield 'different order' => [
			new MixedValueList( 'test-1', 42 ),
			new MixedValueList( 42, 'test-1' ),
			false,
		];

		yield 'different count' => [
			new MixedValueList( 'test-1' ),
			new MixedValueList( 'test-1', 42 ),
			false,
		];

		yield 'unequal types' => [
			new MixedValueList( 'test-1' ),
			new NoIntegerMixedValueList( 'test-1' ),
			false,
		];
	}

	/**
	 * @dataProvider EqualsDataProvider
	 */
	public function testEquals( RepresentsImmutableMixedValueList $originalList, RepresentsImmutableMixedValueList $actualList, bool $expected ): void
	{
		self::assertSame( $expected, $originalList->equals( $actualList ) );
	}

	public static function EqualsValuesDataProvider(): \Iterator
	{
		yield 'same values same order' => [
			[ 'test-1', 42 ],
			[ 'test-1', 42 ],
			true,
		];

		yield 'same values different order' => [
			[ 'test-1', 42 ],
			[ 42, 'test-1' ],
			false,
		];

		yield 'different values' => [
			[ 'test-1' ],
			[ 'test-2' ],
			false,
		];

		yield 'different count' => [
			[ 'test-1' ],
			[ 'test-1', 'test-1' ],
			false,
		];
	}

	/**
	 * @dataProvider EqualsValuesDataProvider
	 */
	public function testEqualsValues( array $values1, array $values2, bool $expected ): void
	{
		$originalList = new MixedValueList( ...$values1 );
		$actualList   = new MixedValueList( ...$values2 );

		self::assertSame( $expected, $originalList->equalsValues( $actualList ) );
	}

	public function testRemove(): void
	{
		$originalList = new MixedValueList( 'test-1', 42 );
		$actualList   = $originalList->remove( 0 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 1, $actualList );
	}

	public function testAppend(): void
	{
		$originalList = new MixedValueList( 'test-1', 42 );
		$actualList   = $originalList->append( 'test-1' );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 'test-1', $actualList->get( 0 ) );
		self::assertSame( 42, $actualList->get( 1 ) );
		self::assertSame( 'test-1', $actualList->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$originalList = new MixedValueList( 'test-1', 42 );
		$actualList   = $originalList->prepend( 'test-1' );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 'test-1', $actualList->get( 0 ) );
		self::assertSame( 'test-1', $actualList->get( 1 ) );
		self::assertSame( 42, $actualList->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$originalList = new MixedValueList( 'test-1', 42 );
		$actualList   = $originalList->appendList( new MixedValueList( 'test-1' ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 'test-1', $actualList->get( 0 ) );
		self::assertSame( 42, $actualList->get( 1 ) );
		self::assertSame( 'test-1', $actualList->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$originalList = new MixedValueList( 'test-1', 42 );
		$actualList   = $originalList->prependList( new MixedValueList( 'test-1' ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 'test-1', $actualList->get( 0 ) );
		self::assertSame( 'test-1', $actualList->get( 1 ) );
		self::assertSame( 42, $actualList->get( 2 ) );
	}

	public function testUnique(): void
	{
		$originalList = new MixedValueList( 'test-1', 42, 'test-1' );
		$actualList   = $originalList->unique();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertSame( 'test-1', $originalList->get( 0 ) );
		self::assertSame( 42, $originalList->get( 1 ) );
	}

	public function testDiff(): void
	{
		$originalList = new MixedValueList( 'test-1', 'test-2' );
		$actualList   = $originalList->diff( new MixedValueList( 'test-2' ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertSame( 'test-1', $actualList->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$originalList = new MixedValueList( 'test-1', 'test-2' );
		$actualList   = $originalList->intersect( new MixedValueList( 'test-2' ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertSame( 'test-2', $actualList->get( 0 ) );
	}

	public function testSplit(): void
	{
		$originalList = new MixedValueList( 'test-1', 'test-2', 'test-3' );
		$chunkedList  = iterator_to_array( $originalList->split( 2 ) );

		self::assertCount( 3, $originalList );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new MixedValueList( 'test-1', 'test-2' ), $chunkedList[0] );
		self::assertEquals( new MixedValueList( 'test-3' ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$originalList = new MixedValueList( 'test-1', 'test-2', 'test-3' );
		$actualList   = $originalList->slice( 1, 1 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new MixedValueList( 'test-2' ), $actualList );
	}

	public function testShuffle(): void
	{
		$originalList = new MixedValueList( 'test-1', 'test-2' );
		$actualList   = $originalList->shuffle();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertEqualsCanonicalizing( new MixedValueList( 'test-1', 'test-2' ), $actualList );
	}

	public function testSort(): void
	{
		$originalList = new MixedValueList( 'test-3', 'test-1' );

		$comparer = new class implements ComparesValues {
			public function compare( int|float|string|bool|array $firstValue, int|float|string|bool|array $secondValue ): int
			{
				return strcmp( (string)$firstValue, (string)$secondValue );
			}
		};

		$actualList = $originalList->sort( $comparer );

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals( new MixedValueList( 'test-1', 'test-3' ), $actualList );
	}

	public function testReverse(): void
	{
		$originalList = new MixedValueList( 'test-1', 'test-2', 'test-3' );
		$actualList   = $originalList->reverse();

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals( new MixedValueList( 'test-3', 'test-2', 'test-1' ), $actualList );
	}

	public static function ReplaceDataProvider(): \Iterator
	{
		yield 'replace first' => [
			[ 'test-2', 'test-1' ],
			0,
			'test-1',
		];

		yield 'replace second' => [
			[ 'test-1', 'test-2' ],
			1,
			'test-1',
		];
	}

	/**
	 * @dataProvider ReplaceDataProvider
	 */
	public function testReplace( array $values, int $index, string $expected ): void
	{
		$originalList = new MixedValueList( ...$values );
		$actualList   = $originalList->replace( $index, $expected );

		self::assertNotSame( $originalList, $actualList );
		self::assertSame( $expected, $actualList->get( $index ) );
	}

	public function testReplaceThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new MixedValueList( 'test-1' );
		$list->replace( 99, 'test-2' );
	}

	public static function ExistsDataProvider(): \Iterator
	{
		yield 'index exists' => [
			[ 'test-1' ],
			0,
			true,
		];

		yield 'index does not exist' => [
			[ 'test-1' ],
			1,
			false,
		];
	}

	/**
	 * @dataProvider ExistsDataProvider
	 */
	public function testExists( array $values, int $index, bool $expected ): void
	{
		$list = new MixedValueList( ...$values );

		self::assertSame( $expected, $list->exists( $index ) );
	}

	public function testGet(): void
	{
		$list = new MixedValueList( 'test-1', 'test-2', 'test-3', 'test-4' );

		self::assertSame( 'test-4', $list->get( 3 ) );
	}

	public function testGetThrowsExceptionIfIndexNotExists(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new MixedValueList( 'test-1' );
		$list->get( 42 );
	}

	public function testFirst(): void
	{
		$list = new MixedValueList( 'test-1', 'test-2' );

		self::assertSame( 'test-1', $list->first() );
	}

	public function testLast(): void
	{
		$list = new MixedValueList( 'test-1', 'test-2' );

		self::assertSame( 'test-2', $list->last() );
	}

	public function testFindIndex(): void
	{
		$list = new MixedValueList( 'test-1', 'test-2' );

		$matcher = new class implements MatchesValue {
			public function matches( int|float|string|bool|array $value ): bool
			{
				return $value === 'test-2';
			}
		};

		self::assertSame( 1, $list->findIndex( $matcher ) );
	}

	public function testFindIndexReturnsNullIfNothingMatched(): void
	{
		$list = new MixedValueList( 'test-1' );

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
		$list = new MixedValueList( 'test-1', 1.1, 2, true, [ 'test-2', 'test-3' ] );

		$json    = $list->toJson();
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 'test-1', 1.1, 2, true, [ 'test-2', 'test-3' ] ], $decoded );
	}

	public function testToJsonPretty(): void
	{
		$list = new MixedValueList( 'test-1', 1.1, 2, true, [ 'test-2', 'test-3' ] );

		$json = $list->toJson( true );

		self::assertStringContainsString( "\n", $json );
		self::assertSame( [ 'test-1', 1.1, 2, true, [ 'test-2', 'test-3' ] ], json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function testJsonSerialize(): void
	{
		$list = new MixedValueList( 'test-1', 1.1, 2, true, [ 'test-2', 'test-3' ] );

		$json    = json_encode( $list );
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 'test-1', 1.1, 2, true, [ 'test-2', 'test-3' ] ], $decoded );
	}

	public function testFromJson(): void
	{
		$list         = MixedValueList::fromJson( '["test-1", 42, 1.1]' );
		$expectedList = new MixedValueList( 'test-1', 42, 1.1 );

		self::assertEquals( $expectedList, $list );
	}

	public function testThrowsInvalidValueExceptionIfValueInvalid(): void
	{
		$this->expectExceptionObject(
			new InvalidMixedValueListException( 'Invalid value in ' . NoIntegerMixedValueList::class )
		);

		new NoIntegerMixedValueList( 42 );
	}

	public function testThrowsExceptionIfKeysAreUsed(): void
	{
		$this->expectExceptionObject(
			new InvalidMixedValueListException( 'Invalid index in ' . MixedValueList::class )
		);

		new MixedValueList( ...[ 'foo' => 'bar' ] );
	}
}
