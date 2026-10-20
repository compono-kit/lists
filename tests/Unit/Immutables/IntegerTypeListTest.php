<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables;

use ComponoKit\Types\Factories\IntegerFactory;
use ComponoKit\Types\IntegerType;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\InvalidIntegerTypeListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Immutables\IntegerTypeList;
use ComponoKit\Types\Lists\Immutables\IntegerValueList;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes\NoTwoIntegerTypeList;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IntegerTypeListTest extends TestCase
{
	public static function EqualsDataProvider(): \Iterator
	{
		yield 'same order' => [
			new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) ),
			new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) ),
			true,
		];

		yield 'different order' => [
			new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) ),
			new IntegerTypeList( new IntegerType( 2 ), new IntegerType( 1 ) ),
			false,
		];

		yield 'different count' => [
			new IntegerTypeList( new IntegerType( 1 ) ),
			new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) ),
			false,
		];

		yield 'unequal types' => [
			new IntegerTypeList( new IntegerType( 1 ) ),
			new NoTwoIntegerTypeList( new IntegerType( 1 ) ),
			false,
		];
	}

	#[DataProvider( 'EqualsDataProvider' )]
	public function testEquals( RepresentsImmutableIntegerTypeList $originalList, RepresentsImmutableIntegerTypeList $actualList, bool $expected ): void
	{
		self::assertSame( $expected, $originalList->equals( $actualList ) );
	}

	public static function EqualsValuesDataProvider(): \Iterator
	{
		yield 'same values same order' => [
			[ new IntegerType( 1 ), new IntegerType( 2 ) ],
			[ new IntegerType( 1 ), new IntegerType( 2 ) ],
			true,
		];

		yield 'same values different order' => [
			[ new IntegerType( 1 ), new IntegerType( 2 ) ],
			[ new IntegerType( 2 ), new IntegerType( 1 ) ],
			false,
		];

		yield 'different values' => [
			[ new IntegerType( 1 ) ],
			[ new IntegerType( 2 ) ],
			false,
		];

		yield 'different count' => [
			[ new IntegerType( 1 ) ],
			[ new IntegerType( 1 ), new IntegerType( 1 ) ],
			false,
		];
	}

	#[DataProvider( 'EqualsValuesDataProvider' )]
	public function testEqualsValues( array $values1, array $values2, bool $expected ): void
	{
		$originalList = new IntegerTypeList( ...$values1 );
		$actualList   = new IntegerTypeList( ...$values2 );

		self::assertSame( $expected, $originalList->equalsValues( $actualList ) );
	}

	public function testRemove(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );
		$actualList   = $originalList->remove( 0 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 1, $actualList );
	}

	public function testAppend(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );
		$actualList   = $originalList->append( new IntegerType( 1 ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new IntegerType( 1 ), $actualList->get( 0 ) );
		self::assertEquals( new IntegerType( 2 ), $actualList->get( 1 ) );
		self::assertEquals( new IntegerType( 1 ), $actualList->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );
		$actualList   = $originalList->prepend( new IntegerType( 1 ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new IntegerType( 1 ), $actualList->get( 0 ) );
		self::assertEquals( new IntegerType( 1 ), $actualList->get( 1 ) );
		self::assertEquals( new IntegerType( 2 ), $actualList->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );
		$actualList   = $originalList->appendList( new IntegerTypeList( new IntegerType( 1 ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new IntegerType( 1 ), $actualList->get( 0 ) );
		self::assertEquals( new IntegerType( 2 ), $actualList->get( 1 ) );
		self::assertEquals( new IntegerType( 1 ), $actualList->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );
		$actualList   = $originalList->prependList( new IntegerTypeList( new IntegerType( 1 ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new IntegerType( 1 ), $actualList->get( 0 ) );
		self::assertEquals( new IntegerType( 1 ), $actualList->get( 1 ) );
		self::assertEquals( new IntegerType( 2 ), $actualList->get( 2 ) );
	}

	public function testUnique(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ), new IntegerType( 1 ) );
		$actualList   = $originalList->unique();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertEquals( new IntegerType( 1 ), $originalList->get( 0 ) );
		self::assertEquals( new IntegerType( 2 ), $originalList->get( 1 ) );
	}

	public function testDiff(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );
		$actualList   = $originalList->diff( new IntegerTypeList( new IntegerType( 2 ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new IntegerType( 1 ), $actualList->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );
		$actualList   = $originalList->intersect( new IntegerTypeList( new IntegerType( 2 ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new IntegerType( 2 ), $actualList->get( 0 ) );
	}

	public function testSplit(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ), new IntegerType( 3 ) );
		$chunkedList  = iterator_to_array( $originalList->split( 2 ) );

		self::assertCount( 3, $originalList );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) ), $chunkedList[0] );
		self::assertEquals( new IntegerTypeList( new IntegerType( 3 ) ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ), new IntegerType( 3 ) );
		$actualList   = $originalList->slice( 1, 1 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new IntegerTypeList( new IntegerType( 2 ) ), $actualList );
	}

	public function testShuffle(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );
		$actualList   = $originalList->shuffle();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertEqualsCanonicalizing(
			new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) ),
			$actualList
		);
	}

	public function testSort(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 3 ), new IntegerType( 1 ) );

		$comparer = new class implements ComparesTypes {
			public function compare( RepresentsType $firstType, RepresentsType $secondType ): int
			{
				return $firstType->toNativeType() <=> $secondType->toNativeType();
			}
		};

		$actualList = $originalList->sort( $comparer );

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals(
			new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 3 ) ),
			$actualList
		);
	}

	public function testReverse(): void
	{
		$originalList = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ), new IntegerType( 3 ) );
		$actualList   = $originalList->reverse();

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals(
			new IntegerTypeList( new IntegerType( 3 ), new IntegerType( 2 ), new IntegerType( 1 ) ),
			$actualList
		);
	}

	public static function ReplaceDataProvider(): \Iterator
	{
		yield 'replace first' => [
			[ new IntegerType( 2 ), new IntegerType( 1 ) ],
			0,
			1,
		];

		yield 'replace second' => [
			[ new IntegerType( 1 ), new IntegerType( 2 ) ],
			1,
			1,
		];
	}

	#[DataProvider( 'ReplaceDataProvider' )]
	public function testReplace( array $values, int $index, int $expected ): void
	{
		$originalList = new IntegerTypeList( ...$values );
		$type1        = new IntegerType( $expected );

		$actualList = $originalList->replace( $index, $type1 );

		self::assertNotSame( $originalList, $actualList );
		self::assertSame( $expected, $actualList->get( $index )->toInteger() );
	}

	public function testReplaceThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new IntegerTypeList( new IntegerType( 1 ) );
		$list->replace( 99, new IntegerType( 2 ) );
	}

	public static function ExistsDataProvider(): \Iterator
	{
		yield 'index exists' => [
			[ new IntegerType( 1 ) ],
			0,
			true,
		];

		yield 'index does not exist' => [
			[ new IntegerType( 1 ) ],
			1,
			false,
		];
	}

	#[DataProvider( 'ExistsDataProvider' )]
	public function testExists( array $values, int $index, bool $expected ): void
	{
		$list = new IntegerTypeList( ...$values );

		self::assertSame( $expected, $list->exists( $index ) );
	}

	public function testGet(): void
	{
		$list = new IntegerTypeList(
			new IntegerType( 1 ),
			new IntegerType( 2 ),
			new IntegerType( 3 ),
			new IntegerType( 4 ),
		);

		self::assertEquals( new IntegerType( 4 ), $list->get( 3 ) );
	}

	public function testGetThrowsExceptionIfIndexNotExists(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new IntegerTypeList( new IntegerType( 1 ) );
		$list->get( 42 );
	}

	public function testFromValueList(): void
	{
		$list         = IntegerTypeList::fromValueList( new IntegerValueList( 2, 1 ), new IntegerFactory() );
		$expectedList = new IntegerTypeList( new IntegerType( 2 ), new IntegerType( 1 ) );

		self::assertEquals( $expectedList, $list );
	}

	public function testToJson(): void
	{
		$list = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );

		$json    = $list->toJson();
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 1, 2 ], $decoded );
	}

	public function testToJsonPretty(): void
	{
		$list = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );

		$json = $list->toJson( true );

		self::assertStringContainsString( "\n", $json );
		self::assertSame( [ 1, 2 ], json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function testJsonSerialize(): void
	{
		$list = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );

		$json    = json_encode( $list );
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 1, 2 ], $decoded );
	}

	public function testFirst(): void
	{
		$list = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );

		self::assertEquals( new IntegerType( 1 ), $list->first() );
	}

	public function testLast(): void
	{
		$list = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );

		self::assertEquals( new IntegerType( 2 ), $list->last() );
	}

	public function testFindIndex(): void
	{
		$list = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );

		$matcher = new class implements MatchesType {
			public function matches( RepresentsType $type ): bool
			{
				return $type->toNativeType() === 2;
			}
		};

		self::assertSame( 1, $list->findIndex( $matcher ) );
	}

	public function testFindIndexReturnsNullIfNothingMatched(): void
	{
		$list = new IntegerTypeList( new IntegerType( 1 ) );

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
		$this->expectExceptionObject(
			new InvalidIntegerTypeListException( 'Invalid value in ' . NoTwoIntegerTypeList::class )
		);

		new NoTwoIntegerTypeList( new IntegerType( 2 ) );
	}

	public function testThrowsExceptionIfKeysAreUsed(): void
	{
		$this->expectExceptionObject(
			new InvalidIntegerTypeListException( 'Invalid index in ' . IntegerTypeList::class )
		);

		new IntegerTypeList( ...[ 'foo' => new IntegerType( 3 ) ] );
	}

	public function testToNativeType(): void
	{
		$type1 = new IntegerType( 1 );
		$type2 = new IntegerType( 2 );
		$list  = new IntegerTypeList( $type1, $type2 );

		self::assertSame( [ $type1, $type2 ], $list->toNativeType() );
	}

	public function testToIntegers(): void
	{
		$list = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );

		self::assertSame( [ 1, 2 ], $list->toIntegers() );
	}

	public function testIterateIntegers(): void
	{
		$list = new IntegerTypeList( new IntegerType( 1 ), new IntegerType( 2 ) );

		self::assertSame( [ 1, 2 ], iterator_to_array( $list->iterateIntegers() ) );
	}
}
