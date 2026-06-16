<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidMixedValueListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableMixedValueList;
use ComponoKit\Types\Lists\Mutables\MixedValueList;
use ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes\NoForbiddenStringMixedValueList;
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
			new NoForbiddenStringMixedValueList( 'test-1' ),
			false,
		];
	}

	/**
	 * @dataProvider EqualsDataProvider
	 */
	public function testEquals( RepresentsMutableMixedValueList $originalList, RepresentsMutableMixedValueList $actualList, bool $expected ): void
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
			[ 42 ],
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
		$list = new MixedValueList( 'test-1', 42 );
		$list->remove( 0 );

		self::assertCount( 1, $list );
	}

	public function testAppend(): void
	{
		$list = new MixedValueList( 'test-1', 42 );
		$list->append( 'test-1' );

		self::assertCount( 3, $list );

		self::assertSame( 'test-1', $list->get( 0 ) );
		self::assertSame( 42, $list->get( 1 ) );
		self::assertSame( 'test-1', $list->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$list = new MixedValueList( 'test-1', 42 );
		$list->prepend( 'test-1' );

		self::assertCount( 3, $list );

		self::assertSame( 'test-1', $list->get( 0 ) );
		self::assertSame( 'test-1', $list->get( 1 ) );
		self::assertSame( 42, $list->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$list = new MixedValueList( 'test-1', 42 );
		$list->appendList( new MixedValueList( 'test-1' ) );

		self::assertCount( 3, $list );

		self::assertSame( 'test-1', $list->get( 0 ) );
		self::assertSame( 42, $list->get( 1 ) );
		self::assertSame( 'test-1', $list->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$list = new MixedValueList( 'test-1', 42 );
		$list->prependList( new MixedValueList( 'test-1' ) );

		self::assertCount( 3, $list );

		self::assertSame( 'test-1', $list->get( 0 ) );
		self::assertSame( 'test-1', $list->get( 1 ) );
		self::assertSame( 42, $list->get( 2 ) );
	}

	public function testUnique(): void
	{
		$list = new MixedValueList( 'test-1', 42, 'test-1' );
		$list->unique();

		self::assertCount( 2, $list );
		self::assertSame( 'test-1', $list->get( 0 ) );
		self::assertSame( 42, $list->get( 1 ) );
	}

	public function testDiff(): void
	{
		$list1 = new MixedValueList( 'test-1', 'test-2' );
		$list2 = $list1->diff( new MixedValueList( 'test-2' ) );

		self::assertCount( 1, $list2 );
		self::assertSame( 'test-1', $list2->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$list1 = new MixedValueList( 'test-1', 'test-2' );
		$list2 = $list1->intersect( new MixedValueList( 'test-2' ) );

		self::assertCount( 1, $list2 );
		self::assertSame( 'test-2', $list2->get( 0 ) );
	}

	public function testSplit(): void
	{
		$list        = new MixedValueList( 'test-1', 42, 1.1 );
		$chunkedList = iterator_to_array( $list->split( 2 ) );

		self::assertCount( 3, $list );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new MixedValueList( 'test-1', 42 ), $chunkedList[0] );
		self::assertEquals( new MixedValueList( 1.1 ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$list = new MixedValueList( 'test-1', 42, 1.1 );
		$list->slice( 1, 1 );

		self::assertCount( 1, $list );
		self::assertEquals( new MixedValueList( 42 ), $list );
	}

	public function testShuffle(): void
	{
		$list = new MixedValueList( 'test-1', 'test-2' );
		$list->shuffle();

		self::assertCount( 2, $list );
		self::assertEqualsCanonicalizing( new MixedValueList( 'test-1', 'test-2' ), $list );
	}

	public function testSort(): void
	{
		$list = new MixedValueList( 'test-3', 'test-1' );

		$comparer = new class implements ComparesValues {
			public function compare( int|float|string|bool|array $firstValue, int|float|string|bool|array $secondValue ): int
			{
				return strcmp( (string)$firstValue, (string)$secondValue );
			}
		};

		$list->sort( $comparer );

		self::assertEquals( new MixedValueList( 'test-1', 'test-3' ), $list );
	}

	public function testReverse(): void
	{
		$list = new MixedValueList( 'test-1', 42, 1.1 );
		$list->reverse();

		self::assertEquals( new MixedValueList( 1.1, 42, 'test-1' ), $list );
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
		$list = new MixedValueList( ...$values );
		$list->replace( $index, $expected );

		self::assertSame( $expected, $list->get( $index ) );
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
		$list = new MixedValueList( 'test-1', 42, 1.1, true );

		self::assertSame( true, $list->get( 3 ) );
	}

	public function testGetThrowsExceptionIfIndexNotExists(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new MixedValueList( 'test-1' );
		$list->get( 42 );
	}

	public function testFirst(): void
	{
		$list = new MixedValueList( 'test-1', 42 );

		self::assertSame( 'test-1', $list->first() );
	}

	public function testLast(): void
	{
		$list = new MixedValueList( 'test-1', 42 );

		self::assertSame( 42, $list->last() );
	}

	public function testFindIndex(): void
	{
		$list = new MixedValueList( 'test-1', 42 );

		$matcher = new class implements MatchesValue {
			public function matches( int|float|string|bool|array $value ): bool
			{
				return $value === 42;
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
			new InvalidMixedValueListException( 'Invalid ' . NoForbiddenStringMixedValueList::class )
		);

		new NoForbiddenStringMixedValueList( 'forbidden' );
	}
}
