<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidFloatValueListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Immutables\FloatValueList;
use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableFloatValueList;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes\NoTwoFloatValueList;
use PHPUnit\Framework\Attributes\DataProvider;
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

	#[DataProvider( 'EqualsDataProvider' )]
	public function testEquals( RepresentsImmutableFloatValueList $originalList, RepresentsImmutableFloatValueList $actualList, bool $expected ): void
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

	#[DataProvider( 'EqualsValuesDataProvider' )]
	public function testEqualsValues( array $values1, array $values2, bool $expected ): void
	{
		$originalList = new FloatValueList( ...$values1 );
		$actualList   = new FloatValueList( ...$values2 );

		self::assertSame( $expected, $originalList->equalsValues( $actualList ) );
	}

	public function testRemove(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2 );
		$actualList   = $originalList->remove( 0 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 1, $actualList );
	}

	public function testAppend(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2 );
		$actualList   = $originalList->append( 1.1 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 1.1, $actualList->get( 0 ) );
		self::assertSame( 2.2, $actualList->get( 1 ) );
		self::assertSame( 1.1, $actualList->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2 );
		$actualList   = $originalList->prepend( 1.1 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 1.1, $actualList->get( 0 ) );
		self::assertSame( 1.1, $actualList->get( 1 ) );
		self::assertSame( 2.2, $actualList->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2 );
		$actualList   = $originalList->appendList( new FloatValueList( 1.1 ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 1.1, $actualList->get( 0 ) );
		self::assertSame( 2.2, $actualList->get( 1 ) );
		self::assertSame( 1.1, $actualList->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2 );
		$actualList   = $originalList->prependList( new FloatValueList( 1.1 ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertSame( 1.1, $actualList->get( 0 ) );
		self::assertSame( 1.1, $actualList->get( 1 ) );
		self::assertSame( 2.2, $actualList->get( 2 ) );
	}

	public function testUnique(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2, 1.1 );
		$actualList   = $originalList->unique();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertSame( 1.1, $originalList->get( 0 ) );
		self::assertSame( 2.2, $originalList->get( 1 ) );
	}

	public function testDiff(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2 );
		$actualList   = $originalList->diff( new FloatValueList( 2.2 ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertSame( 1.1, $actualList->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2 );
		$actualList   = $originalList->intersect( new FloatValueList( 2.2 ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertSame( 2.2, $actualList->get( 0 ) );
	}

	public function testSplit(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2, 3.3 );
		$chunkedList  = iterator_to_array( $originalList->split( 2 ) );

		self::assertCount( 3, $originalList );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new FloatValueList( 1.1, 2.2 ), $chunkedList[0] );
		self::assertEquals( new FloatValueList( 3.3 ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2, 3.3 );
		$actualList   = $originalList->slice( 1, 1 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new FloatValueList( 2.2 ), $actualList );
	}

	public function testShuffle(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2 );
		$actualList   = $originalList->shuffle();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertEqualsCanonicalizing( new FloatValueList( 1.1, 2.2 ), $actualList );
	}

	public function testSort(): void
	{
		$originalList = new FloatValueList( 3.3, 1.1 );

		$comparer = new class implements ComparesValues {
			public function compare( int|float|string|bool|array $firstValue, int|float|string|bool|array $secondValue ): int
			{
				return $firstValue <=> $secondValue;
			}
		};

		$actualList = $originalList->sort( $comparer );

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals( new FloatValueList( 1.1, 3.3 ), $actualList );
	}

	public function testReverse(): void
	{
		$originalList = new FloatValueList( 1.1, 2.2, 3.3 );
		$actualList   = $originalList->reverse();

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals( new FloatValueList( 3.3, 2.2, 1.1 ), $actualList );
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

	#[DataProvider( 'ReplaceDataProvider' )]
	public function testReplace( array $values, int $index, float $expected ): void
	{
		$originalList = new FloatValueList( ...$values );
		$actualList   = $originalList->replace( $index, $expected );

		self::assertNotSame( $originalList, $actualList );
		self::assertSame( $expected, $actualList->get( $index ) );
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

	#[DataProvider( 'ExistsDataProvider' )]
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
			new InvalidFloatValueListException( 'Invalid value in ' . NoTwoFloatValueList::class )
		);

		new NoTwoFloatValueList( 2.2 );
	}

	public function testThrowsExceptionIfKeysAreUsed(): void
	{
		$this->expectExceptionObject(
			new InvalidFloatValueListException( 'Invalid index in ' . FloatValueList::class )
		);

		new FloatValueList( ...[ 'foo' => 1.1 ] );
	}
}
