<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\InvalidStringTypeListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableStringTypeList;
use ComponoKit\Types\Lists\Mutables\StringTypeList;
use ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes\NoTwoStringTypeList;
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
	public function testEquals( RepresentsMutableStringTypeList $originalList, RepresentsMutableStringTypeList $actualList, bool $expected ): void
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
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->remove( 0 );

		self::assertCount( 1, $list );
	}

	public function testAppend(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->append( new StringType( 'test-1' ) );

		self::assertCount( 3, $list );

		self::assertEquals( new StringType( 'test-1' ), $list->get( 0 ) );
		self::assertEquals( new StringType( 'test-2' ), $list->get( 1 ) );
		self::assertEquals( new StringType( 'test-1' ), $list->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->prepend( new StringType( 'test-1' ) );

		self::assertCount( 3, $list );

		self::assertEquals( new StringType( 'test-1' ), $list->get( 0 ) );
		self::assertEquals( new StringType( 'test-1' ), $list->get( 1 ) );
		self::assertEquals( new StringType( 'test-2' ), $list->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->appendList( new StringTypeList( new StringType( 'test-1' ) ) );

		self::assertCount( 3, $list );

		self::assertEquals( new StringType( 'test-1' ), $list->get( 0 ) );
		self::assertEquals( new StringType( 'test-2' ), $list->get( 1 ) );
		self::assertEquals( new StringType( 'test-1' ), $list->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->prependList( new StringTypeList( new StringType( 'test-1' ) ) );

		self::assertCount( 3, $list );

		self::assertEquals( new StringType( 'test-1' ), $list->get( 0 ) );
		self::assertEquals( new StringType( 'test-1' ), $list->get( 1 ) );
		self::assertEquals( new StringType( 'test-2' ), $list->get( 2 ) );
	}

	public function testUnique(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-1' ) );
		$list->unique();

		self::assertCount( 2, $list );
		self::assertEquals( new StringType( 'test-1' ), $list->get( 0 ) );
		self::assertEquals( new StringType( 'test-2' ), $list->get( 1 ) );
	}

	public function testDiff(): void
	{
		$list1 = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list2 = $list1->diff( new StringTypeList( new StringType( 'test-2' ) ) );

		self::assertCount( 1, $list2 );
		self::assertEquals( new StringType( 'test-1' ), $list2->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$list1 = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list2 = $list1->intersect( new StringTypeList( new StringType( 'test-2' ) ) );

		self::assertCount( 1, $list2 );
		self::assertEquals( new StringType( 'test-2' ), $list2->get( 0 ) );
	}

	public function testSplit(): void
	{
		$list        = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-3' ) );
		$chunkedList = iterator_to_array( $list->split( 2 ) );

		self::assertCount( 3, $list );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) ), $chunkedList[0] );
		self::assertEquals( new StringTypeList( new StringType( 'test-3' ) ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-3' ) );
		$list->slice( 1, 1 );

		self::assertCount( 1, $list );
		self::assertEquals( new StringTypeList( new StringType( 'test-2' ) ), $list );
	}

	public function testShuffle(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->shuffle();

		self::assertCount( 2, $list );
		self::assertEqualsCanonicalizing( new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) ), $list );
	}

	public function testSort(): void
	{
		$list = new StringTypeList( new StringType( 'test-3' ), new StringType( 'test-1' ) );

		$comparer = new class implements ComparesTypes {
			public function compare( RepresentsType $firstType, RepresentsType $secondType ): int
			{
				return $firstType->toNativeType() <=> $secondType->toNativeType();
			}
		};

		$list->sort( $comparer );

		self::assertEquals(
			new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-3' ) ),
			$list
		);
	}

	public function testReverse(): void
	{
		$list = new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-3' ) );
		$list->reverse();

		self::assertEquals(
			new StringTypeList( new StringType( 'test-3' ), new StringType( 'test-2' ), new StringType( 'test-1' ) ),
			$list
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
		$list  = new StringTypeList( ...$values );
		$type1 = new StringType( $expected );

		$list->replace( $index, $type1 );

		self::assertSame( $expected, $list->get( $index )->toString() );
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
		$this->expectExceptionObject( new InvalidStringTypeListException( 'Invalid ' . NoTwoStringTypeList::class ) );

		new NoTwoStringTypeList( new StringType( 'test-2' ) );
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
