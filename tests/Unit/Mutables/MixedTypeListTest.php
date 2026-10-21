<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables;

use ComponoKit\Types\BooleanType;
use ComponoKit\Types\FloatType;
use ComponoKit\Types\IntegerType;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\InvalidMixedTypeListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableMixedTypeList;
use ComponoKit\Types\Lists\Mutables\MixedTypeList;
use ComponoKit\Types\Lists\Mutables\StringTypeList;
use ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes\NoFloatTypeList;
use ComponoKit\Types\StringType;
use PHPUnit\Framework\TestCase;

class MixedTypeListTest extends TestCase
{
	public static function EqualsDataProvider(): \Iterator
	{
		yield 'same order' => [
			new MixedTypeList(
				new StringType( 'test-1' ),
				new FloatType( 1.1 ),
				new IntegerType( 2 ),
				new BooleanType( true ),
				new StringTypeList( new StringType( 'test-2' ), new StringType( 'test-3' ) )
			),
			new MixedTypeList(
				new StringType( 'test-1' ),
				new FloatType( 1.1 ),
				new IntegerType( 2 ),
				new BooleanType( true ),
				new StringTypeList( new StringType( 'test-2' ), new StringType( 'test-3' ) )
			),
			true,
		];

		yield 'different order' => [
			new MixedTypeList(
				new BooleanType( true ),
				new StringType( 'test-1' ),
				new FloatType( 1.1 ),
				new IntegerType( 2 ),
				new StringTypeList( new StringType( 'test-2' ), new StringType( 'test-3' ) )
			),
			new MixedTypeList(
				new StringType( 'test-1' ),
				new FloatType( 1.1 ),
				new IntegerType( 2 ),
				new BooleanType( true ),
				new StringTypeList( new StringType( 'test-2' ), new StringType( 'test-3' ) )
			),
			false,
		];

		yield 'different count' => [
			new MixedTypeList(
				new BooleanType( true ),
				new StringType( 'test-1' ),
				new FloatType( 1.1 ),
				new IntegerType( 2 ),
				new StringTypeList( new StringType( 'test-2' ), new StringType( 'test-3' ) )
			),
			new MixedTypeList(
				new BooleanType( true ),
				new StringType( 'test-1' ),
				new IntegerType( 2 ),
				new StringTypeList( new StringType( 'test-2' ), new StringType( 'test-3' ) )
			),
			false,
		];

		yield 'unequal types' => [
			new MixedTypeList( new StringType( 'test-1' ) ),
			new NoFloatTypeList( new StringType( 'test-1' ), ),
			false,
		];
	}

	/**
	 * @dataProvider EqualsDataProvider
	 */
	public function testEquals( RepresentsMutableMixedTypeList $originalList, RepresentsMutableMixedTypeList $actualList, bool $expected ): void
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
		$originalList = new MixedTypeList( ...$values1 );
		$actualList   = new MixedTypeList( ...$values2 );

		self::assertSame( $expected, $originalList->equalsValues( $actualList ) );
	}

	public function testRemove(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->remove( 0 );

		self::assertCount( 1, $list );
	}

	public function testAppend(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->append( new StringType( 'test-1' ) );

		self::assertCount( 3, $list );

		self::assertEquals( new StringType( 'test-1' ), $list->get( 0 ) );
		self::assertEquals( new StringType( 'test-2' ), $list->get( 1 ) );
		self::assertEquals( new StringType( 'test-1' ), $list->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->prepend( new StringType( 'test-1' ) );

		self::assertCount( 3, $list );

		self::assertEquals( new StringType( 'test-1' ), $list->get( 0 ) );
		self::assertEquals( new StringType( 'test-1' ), $list->get( 1 ) );
		self::assertEquals( new StringType( 'test-2' ), $list->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->appendList( new MixedTypeList( new StringType( 'test-1' ) ) );

		self::assertCount( 3, $list );

		self::assertEquals( new StringType( 'test-1' ), $list->get( 0 ) );
		self::assertEquals( new StringType( 'test-2' ), $list->get( 1 ) );
		self::assertEquals( new StringType( 'test-1' ), $list->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->prependList( new MixedTypeList( new StringType( 'test-1' ) ) );

		self::assertCount( 3, $list );

		self::assertEquals( new StringType( 'test-1' ), $list->get( 0 ) );
		self::assertEquals( new StringType( 'test-1' ), $list->get( 1 ) );
		self::assertEquals( new StringType( 'test-2' ), $list->get( 2 ) );
	}

	public function testUnique(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-1' ) );
		$list->unique();

		self::assertCount( 2, $list );
		self::assertEquals( new StringType( 'test-1' ), $list->get( 0 ) );
		self::assertEquals( new StringType( 'test-2' ), $list->get( 1 ) );
	}

	public function testUniqueWithNestedLists(): void
	{
		$nested = new StringTypeList( new StringType( 'test-1' ) );
		$list   = new MixedTypeList(
			$nested,
			new StringTypeList( new StringType( 'test-1' ) ),
			new StringType( 'other' ),
		);
		$list->unique();

		self::assertCount( 2, $list );
		self::assertEquals( $nested, $list->get( 0 ) );
		self::assertEquals( new StringType( 'other' ), $list->get( 1 ) );
	}

	public function testDiff(): void
	{
		$list1 = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list2 = $list1->diff( new MixedTypeList( new StringType( 'test-2' ) ) );

		self::assertCount( 1, $list2 );
		self::assertEquals( new StringType( 'test-1' ), $list2->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$list1 = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list2 = $list1->intersect( new MixedTypeList( new StringType( 'test-2' ) ) );

		self::assertCount( 1, $list2 );
		self::assertEquals( new StringType( 'test-2' ), $list2->get( 0 ) );
	}

	public function testSplit(): void
	{
		$list        = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-3' ) );
		$chunkedList = iterator_to_array( $list->split( 2 ) );

		self::assertCount( 3, $list );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) ), $chunkedList[0] );
		self::assertEquals( new MixedTypeList( new StringType( 'test-3' ) ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-3' ) );
		$list->slice( 1, 1 );

		self::assertCount( 1, $list );
		self::assertEquals( new MixedTypeList( new StringType( 'test-2' ) ), $list );
	}

	public function testShuffle(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );
		$list->shuffle();

		self::assertCount( 2, $list );
		self::assertEqualsCanonicalizing( new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) ), $list );
	}

	public function testSort(): void
	{
		$list = new MixedTypeList( new StringType( 'test-3' ), new StringType( 'test-1' ) );

		$comparer = new class implements ComparesTypes {
			public function compare( RepresentsType $firstType, RepresentsType $secondType ): int
			{
				return $firstType->toNativeType() <=> $secondType->toNativeType();
			}
		};

		$list->sort( $comparer );

		self::assertEquals(
			new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-3' ) ),
			$list
		);
	}

	public function testReverse(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), new StringType( 'test-3' ) );
		$list->reverse();

		self::assertEquals(
			new MixedTypeList( new StringType( 'test-3' ), new StringType( 'test-2' ), new StringType( 'test-1' ) ),
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
		$list  = new MixedTypeList( ...$values );
		$type1 = new StringType( $expected );

		$list->replace( $index, $type1 );

		self::assertSame( $expected, $list->get( $index )->toString() );
	}

	public function testReplaceThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new MixedTypeList( new StringType( 'test-1' ) );
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
		$list = new MixedTypeList( ...$values );

		self::assertSame( $expected, $list->exists( $index ) );
	}

	public function testGet(): void
	{
		$list = new MixedTypeList(
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

		$list = new MixedTypeList( new StringType( 'test-1' ) );
		$list->get( 42 );
	}

	public function testToJson(): void
	{
		$list = new MixedTypeList(
			new StringType( 'test-1' ),
			new FloatType( 1.1 ),
			new IntegerType( 2 ),
			new BooleanType( true ),
			new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), ),
			new MixedTypeList( new BooleanType( false ), new StringType( 'test-3' ), new IntegerType( 200 ), new MixedTypeList( new FloatType( 1.0 ), new FloatType( 2.5 ) ) )
		);

		$json    = $list->toJson();
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame(
			[
				'test-1',
				1.1,
				2,
				true,
				[ 'test-1', 'test-2' ],
				[ false, 'test-3', 200, [ 1, 2.5 ] ],
			],
			$decoded
		);
	}

	public function testToJsonPretty(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new IntegerType( 2 ), new BooleanType( true ) );

		$json = $list->toJson( true );

		self::assertStringContainsString( "\n", $json );
		self::assertSame( [ 'test-1', 2, true ], json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function testJsonSerialize(): void
	{
		$list = new MixedTypeList(
			new StringType( 'test-1' ),
			new FloatType( 1.1 ),
			new IntegerType( 2 ),
			new BooleanType( true ),
			new StringTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ), ),
			new MixedTypeList( new BooleanType( false ), new StringType( 'test-3' ), new IntegerType( 200 ), new MixedTypeList( new FloatType( 1.0 ), new FloatType( 2.5 ) ) )
		);

		$json    = json_encode( $list );
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame(
			[
				'test-1',
				1.1,
				2,
				true,
				[ 'test-1', 'test-2' ],
				[ false, 'test-3', 200, [ 1, 2.5 ] ],
			],
			$decoded
		);
	}

	public function testFirst(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );

		self::assertEquals( new StringType( 'test-1' ), $list->first() );
	}

	public function testLast(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );

		self::assertEquals( new StringType( 'test-2' ), $list->last() );
	}

	public function testFindIndex(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new StringType( 'test-2' ) );

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
		$list = new MixedTypeList( new StringType( 'test-1' ) );

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
		$this->expectExceptionObject( new InvalidMixedTypeListException( 'Invalid ' . NoFloatTypeList::class ) );

		new NoFloatTypeList( new FloatType( 1.0 ) );
	}

	public function testToNativeType(): void
	{
		$type1 = new StringType( 'test-1' );
		$type2 = new IntegerType( 2 );
		$list  = new MixedTypeList( $type1, $type2 );

		self::assertSame( [ $type1, $type2 ], $list->toNativeType() );
	}

	public function testToNativeTypes(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new IntegerType( 2 ), new BooleanType( true ) );

		self::assertSame( [ 'test-1', 2, true ], $list->toNativeTypes() );
	}

	public function testIterateNativeTypes(): void
	{
		$list = new MixedTypeList( new StringType( 'test-1' ), new IntegerType( 2 ), new BooleanType( true ) );

		self::assertSame( [ 'test-1', 2, true ], iterator_to_array( $list->iterateNativeTypes() ) );
	}
}
