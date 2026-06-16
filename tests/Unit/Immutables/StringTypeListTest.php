<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables;

use ComponoKit\Types\Factories\StringFactory;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\InvalidStringTypeListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Immutables\StringTypeList;
use ComponoKit\Types\Lists\Immutables\StringValueList;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableStringTypeList;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes\NoTwoStringTypeList;
use ComponoKit\Types\StringType;
use PHPUnit\Framework\TestCase;

class StringTypeListTest extends TestCase
{
	public static function EqualsDataProvider(): \Iterator
	{
		yield 'same order' => [
			new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) ),
			new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) ),
			true,
		];

		yield 'different order' => [
			new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) ),
			new StringTypeList( new StringType( 'test-2' ), new StringType( 'test-1' ) ),
			false,
		];

		yield 'different count' => [
			new StringTypeList( new StringType( 'test-1' ) ),
			new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) ),
			false,
		];

		yield 'unequal types' => [
			new StringTypeList( new StringType( 'test-1' ) ),
			new NoTwoStringTypeList( new StringType( 'test-1' ), ),
			false,
		];
	}

	/**
	 * @dataProvider EqualsDataProvider
	 */
	public function testEquals( RepresentsImmutableStringTypeList $originalList, RepresentsImmutableStringTypeList $actualList, bool $expected ): void
	{
		self::assertSame( $expected, $originalList->equals( $actualList ) );
	}

	public static function EqualsValuesDataProvider(): \Iterator
	{
		yield 'same values same order' => [
			[ new StringType( 'test-1' ), new StringType( 'test-2' ) ],
			[ new StringType( 'test-1' ), new StringType( 'test-2' ) ],
			true,
		];

		yield 'same values different order' => [
			[ new StringType( 'test-1' ), new StringType( 'test-2' ) ],
			[ new StringType( 'test-2' ), new StringType( 'test-1' ) ],
			false,
		];

		yield 'different values' => [
			[ new StringType( 'test-1' ) ],
			[ new StringType( 'test-2' ) ],
			false,
		];

		yield 'different count' => [
			[ new StringType( 'test-1' ) ],
			[ new StringType( 'test-1' ), new StringType( 'test-1' ), ],
			false,
		];
	}

	/**
	 * @dataProvider EqualsValuesDataProvider
	 */
	public function testEqualsValues( array $values1, array $values2, bool $expected ): void
	{
		$originalList = new StringTypeList( ...$values1 );
		$actualList   = new StringTypeList( ...$values2 );

		self::assertSame( $expected, $originalList->equalsValues( $actualList ) );
	}

	public function testRemove(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$actualList   = $originalList->remove( 0 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 1, $actualList );
	}

	public function testAppend(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$actualList   = $originalList->append( new StringType( 'test-1' ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new StringType( 'test-1' ), $actualList->get( 0 ) );
		self::assertEquals( new StringType( 'test-2' ), $actualList->get( 1 ) );
		self::assertEquals( new StringType( 'test-1' ), $actualList->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$actualList   = $originalList->prepend( new StringType( 'test-1' ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new StringType( 'test-1' ), $actualList->get( 0 ) );
		self::assertEquals( new StringType( 'test-1' ), $actualList->get( 1 ) );
		self::assertEquals( new StringType( 'test-2' ), $actualList->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$actualList   = $originalList->appendList( new StringTypeList( new StringType( 'test-1' ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new StringType( 'test-1' ), $actualList->get( 0 ) );
		self::assertEquals( new StringType( 'test-2' ), $actualList->get( 1 ) );
		self::assertEquals( new StringType( 'test-1' ), $actualList->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$actualList   = $originalList->prependList( new StringTypeList( new StringType( 'test-1' ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new StringType( 'test-1' ), $actualList->get( 0 ) );
		self::assertEquals( new StringType( 'test-1' ), $actualList->get( 1 ) );
		self::assertEquals( new StringType( 'test-2' ), $actualList->get( 2 ) );
	}

	public function testUnique(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-1' ) );
		$actualList   = $originalList->unique();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertEquals( new StringType( 'test-1' ), $originalList->get( 0 ) );
		self::assertEquals( new StringType( 'test-2' ), $originalList->get( 1 ) );
	}

	public function testDiff(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$actualList   = $originalList->diff( new StringTypeList( new StringType( 'test-2' ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new StringType( 'test-1' ), $actualList->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$actualList   = $originalList->intersect( new StringTypeList( new StringType( 'test-2' ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new StringType( 'test-2' ), $actualList->get( 0 ) );
	}

	public function testSplit(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-3' ) );
		$chunkedList  = iterator_to_array( $originalList->split( 2 ) );

		self::assertCount( 3, $originalList );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) ), $chunkedList[0] );
		self::assertEquals( new StringTypeList( new StringType( 'test-3' ) ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-3' ) );
		$actualList   = $originalList->slice( 1, 1 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new StringTypeList( new StringType( 'test-2' ) ), $actualList );
	}

	public function testShuffle(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$actualList   = $originalList->shuffle();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertEqualsCanonicalizing( new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) ), $actualList );
	}

	public function testSort(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-3' ), new StringType( 'test-1' ) );

		$comparer = new class implements ComparesTypes {
			public function compare( RepresentsType $firstType, RepresentsType $secondType ): int
			{
				return $firstType->toNativeType() <=> $secondType->toNativeType();
			}
		};

		$actualList = $originalList->sort( $comparer );

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals(
			new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-3' ) ),
			$actualList
		);
	}

	public function testReverse(): void
	{
		$originalList = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-3' ) );
		$actualList   = $originalList->reverse();

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals(
			new StringTypeList( new StringType( 'test-3' ), new StringType( 'test-2' ), new StringType( 'test-1' ) ),
			$actualList
		);
	}

	public static function ReplaceDataProvider(): \Iterator
	{
		yield 'replace first' => [
			[ new StringType( 'test-2' ), new StringType( 'test-1' ) ],
			0,
			'test-1',
		];

		yield 'replace second' => [
			[ new StringType( 'test-1' ), new StringType( 'test-2' ) ],
			1,
			'test-1',
		];
	}

	/**
	 * @dataProvider ReplaceDataProvider
	 */
	public function testReplace( array $values, int $index, string $expected ): void
	{
		$originalList = new StringTypeList( ...$values );
		$type1        = new StringType( $expected );

		$actualList = $originalList->replace( $index, $type1 );

		self::assertNotSame( $originalList, $actualList );
		self::assertSame( $expected, $actualList->get( $index )->toString() );
	}

	public function testReplaceThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new StringTypeList( new StringType( 'test-1' ) );
		$list->replace( 99, new StringType( 'test-2' ) );
	}

	public static function ExistsDataProvider(): \Iterator
	{
		yield 'index exists' => [
			[ new StringType( 'test-1' ) ],
			0,
			true,
		];

		yield 'index does not exist' => [
			[ new StringType( 'test-1' ) ],
			1,
			false,
		];
	}

	/**
	 * @dataProvider ExistsDataProvider
	 */
	public function testExists( array $values, int $index, bool $expected ): void
	{
		$list = new StringTypeList( ...$values );

		self::assertSame( $expected, $list->exists( $index ) );
	}

	public function testGet(): void
	{
		$list = new StringTypeList(
			new StringType( 'test-1' ),
			new StringType( 'test-2' ),
			new StringType( 'test-3' ),
			new StringType( 'test-4' ),
		);

		self::assertEquals( new StringType( 'test-4' ), $list->get( 3 ) );
	}

	public function testGetThrowsExceptionIfIndexNotExists(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new StringTypeList( new StringType( 'test-1' ) );
		$list->get( 42 );
	}

	public function testFromValueList(): void
	{
		$list         = StringTypeList::fromValueList( new StringValueList( 'test-2', 'test-1' ), new StringFactory() );
		$expectedList = new StringTypeList( new StringType( 'test-2' ), new StringType( 'test-1' ) );

		self::assertEquals( $expectedList, $list );
	}

	public function testToJson(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );

		$json    = $list->toJson();
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 'test-1', 'test-2' ], $decoded );
	}

	public function testToJsonPretty(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );

		$json = $list->toJson( true );

		self::assertStringContainsString( "\n", $json );
		self::assertSame( [ 'test-1', 'test-2' ], json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function testJsonSerialize(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );

		$json    = json_encode( $list );
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 'test-1', 'test-2' ], $decoded );
	}

	public function testFirst(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );

		self::assertEquals( new StringType( 'test-1' ), $list->first() );
	}

	public function testLast(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );

		self::assertEquals( new StringType( 'test-2' ), $list->last() );
	}

	public function testFindIndex(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );

		$matcher = new class implements MatchesType {
			public function matches( RepresentsType $type ): bool
			{
				return $type->toNativeType() === 'test-2';
			}
		};

		self::assertSame( 1, $list->findIndex( $matcher ) );
	}

	public function testFindIndexReturnsNullIfNothingMatched(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ) );

		$matcher = new class implements MatchesType {
			public function matches( RepresentsType $type ): bool
			{
				return false;
			}
		};

		self::assertNull( $list->findIndex( $matcher ) );
	}

	public function testThrowsInvalidTypeExceptionIfValueInvalid(): void
	{
		$this->expectExceptionObject( new InvalidStringTypeListException( 'Invalid value in ' . NoTwoStringTypeList::class ) );

		new NoTwoStringTypeList( new StringType( 'test-2' ) );
	}

	public function testThrowsExceptionIfKeysAreUsed(): void
	{
		$this->expectExceptionObject(
			new InvalidStringTypeListException( 'Invalid index in ' . StringTypeList::class )
		);

		new StringTypeList( ...[ 'foo' => new StringType( 'bar' ) ] );
	}

	public function testToNativeType(): void
	{
		$type1 = new StringType( 'test-1' );
		$type2 = new StringType( 'test-2' );
		$list  = new StringTypeList( $type1, $type2 );

		self::assertSame( [ $type1, $type2 ], $list->toNativeType() );
	}

	public function testToStrings(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );

		self::assertSame( [ 'test-1', 'test-2' ], $list->toStrings() );
	}

	public function testIterateStrings(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );

		self::assertSame( [ 'test-1', 'test-2' ], iterator_to_array( $list->iterateStrings() ) );
	}
}
