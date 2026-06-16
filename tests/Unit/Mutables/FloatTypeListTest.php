<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables;

use ComponoKit\Types\FloatType;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\InvalidFloatTypeListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableFloatTypeList;
use ComponoKit\Types\Lists\Mutables\FloatTypeList;
use ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes\NoTwoFloatTypeList;
use PHPUnit\Framework\TestCase;

class FloatTypeListTest extends TestCase
{
	public static function EqualsDataProvider(): \Iterator
	{
		yield 'same order' => [
			new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) ),
			new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) ),
			true,
		];

		yield 'different order' => [
			new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) ),
			new FloatTypeList( new FloatType( 2.2 ), new FloatType( 1.1 ) ),
			false,
		];

		yield 'different count' => [
			new FloatTypeList( new FloatType( 1.1 ) ),
			new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) ),
			false,
		];

		yield 'unequal types' => [
			new FloatTypeList( new FloatType( 1.1 ) ),
			new NoTwoFloatTypeList( new FloatType( 1.1 ) ),
			false,
		];
	}

	/**
	 * @dataProvider EqualsDataProvider
	 */
	public function testEquals( RepresentsMutableFloatTypeList $originalList, RepresentsMutableFloatTypeList $actualList, bool $expected ): void
	{
		self::assertSame( $expected, $originalList->equals( $actualList ) );
	}

	public static function EqualsValuesDataProvider(): \Iterator
	{
		yield 'same values same order' => [
			[ new FloatType( 1.1 ), new FloatType( 2.2 ) ],
			[ new FloatType( 1.1 ), new FloatType( 2.2 ) ],
			true,
		];

		yield 'same values different order' => [
			[ new FloatType( 1.1 ), new FloatType( 2.2 ) ],
			[ new FloatType( 2.2 ), new FloatType( 1.1 ) ],
			false,
		];

		yield 'different values' => [
			[ new FloatType( 1.1 ) ],
			[ new FloatType( 2.2 ) ],
			false,
		];

		yield 'different count' => [
			[ new FloatType( 1.1 ) ],
			[ new FloatType( 1.1 ), new FloatType( 1.1 ) ],
			false,
		];
	}

	/**
	 * @dataProvider EqualsValuesDataProvider
	 */
	public function testEqualsValues( array $values1, array $values2, bool $expected ): void
	{
		$originalList = new FloatTypeList( ...$values1 );
		$actualList   = new FloatTypeList( ...$values2 );

		self::assertSame( $expected, $originalList->equalsValues( $actualList ) );
	}

	public function testRemove(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$list->remove( 0 );

		self::assertCount( 1, $list );
	}

	public function testAppend(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$list->append( new FloatType( 1.1 ) );

		self::assertCount( 3, $list );

		self::assertEquals( new FloatType( 1.1 ), $list->get( 0 ) );
		self::assertEquals( new FloatType( 2.2 ), $list->get( 1 ) );
		self::assertEquals( new FloatType( 1.1 ), $list->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$list->prepend( new FloatType( 1.1 ) );

		self::assertCount( 3, $list );

		self::assertEquals( new FloatType( 1.1 ), $list->get( 0 ) );
		self::assertEquals( new FloatType( 1.1 ), $list->get( 1 ) );
		self::assertEquals( new FloatType( 2.2 ), $list->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$list->appendList( new FloatTypeList( new FloatType( 1.1 ) ) );

		self::assertCount( 3, $list );

		self::assertEquals( new FloatType( 1.1 ), $list->get( 0 ) );
		self::assertEquals( new FloatType( 2.2 ), $list->get( 1 ) );
		self::assertEquals( new FloatType( 1.1 ), $list->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$list->prependList( new FloatTypeList( new FloatType( 1.1 ) ) );

		self::assertCount( 3, $list );

		self::assertEquals( new FloatType( 1.1 ), $list->get( 0 ) );
		self::assertEquals( new FloatType( 1.1 ), $list->get( 1 ) );
		self::assertEquals( new FloatType( 2.2 ), $list->get( 2 ) );
	}

	public function testUnique(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ), new FloatType( 1.1 ) );
		$list->unique();

		self::assertCount( 2, $list );
		self::assertEquals( new FloatType( 1.1 ), $list->get( 0 ) );
		self::assertEquals( new FloatType( 2.2 ), $list->get( 1 ) );
	}

	public function testDiff(): void
	{
		$list1 = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$list2 = $list1->diff( new FloatTypeList( new FloatType( 2.2 ) ) );

		self::assertCount( 1, $list2 );
		self::assertEquals( new FloatType( 1.1 ), $list2->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$list1 = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$list2 = $list1->intersect( new FloatTypeList( new FloatType( 2.2 ) ) );

		self::assertCount( 1, $list2 );
		self::assertEquals( new FloatType( 2.2 ), $list2->get( 0 ) );
	}

	public function testSplit(): void
	{
		$list        = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ), new FloatType( 3.3 ) );
		$chunkedList = iterator_to_array( $list->split( 2 ) );

		self::assertCount( 3, $list );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) ), $chunkedList[0] );
		self::assertEquals( new FloatTypeList( new FloatType( 3.3 ) ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ), new FloatType( 3.3 ) );
		$list->slice( 1, 1 );

		self::assertCount( 1, $list );
		self::assertEquals( new FloatTypeList( new FloatType( 2.2 ) ), $list );
	}

	public function testShuffle(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$list->shuffle();

		self::assertCount( 2, $list );
		self::assertEqualsCanonicalizing(
			new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) ),
			$list
		);
	}

	public function testSort(): void
	{
		$list = new FloatTypeList( new FloatType( 3.3 ), new FloatType( 1.1 ) );

		$comparer = new class implements ComparesTypes {
			public function compare( RepresentsType $firstType, RepresentsType $secondType ): int
			{
				return $firstType->toNativeType() <=> $secondType->toNativeType();
			}
		};

		$list->sort( $comparer );

		self::assertEquals(
			new FloatTypeList( new FloatType( 1.1 ), new FloatType( 3.3 ) ),
			$list
		);
	}

	public function testReverse(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ), new FloatType( 3.3 ) );
		$list->reverse();

		self::assertEquals(
			new FloatTypeList( new FloatType( 3.3 ), new FloatType( 2.2 ), new FloatType( 1.1 ) ),
			$list
		);
	}

	public static function ReplaceDataProvider(): \Iterator
	{
		yield 'replace first' => [
			[ new FloatType( 2.2 ), new FloatType( 1.1 ) ],
			0,
			1.1,
		];

		yield 'replace second' => [
			[ new FloatType( 1.1 ), new FloatType( 2.2 ) ],
			1,
			1.1,
		];
	}

	/**
	 * @dataProvider ReplaceDataProvider
	 */
	public function testReplace( array $values, int $index, float $expected ): void
	{
		$list  = new FloatTypeList( ...$values );
		$type1 = new FloatType( $expected );

		$list->replace( $index, $type1 );

		self::assertSame( $expected, $list->get( $index )->toFloat() );
	}

	public function testReplaceThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new FloatTypeList( new FloatType( 1.1 ) );
		$list->replace( 99, new FloatType( 2.2 ) );
	}

	public static function ExistsDataProvider(): \Iterator
	{
		yield 'index exists' => [
			[ new FloatType( 1.1 ) ],
			0,
			true,
		];

		yield 'index does not exist' => [
			[ new FloatType( 1.1 ) ],
			1,
			false,
		];
	}

	/**
	 * @dataProvider ExistsDataProvider
	 */
	public function testExists( array $values, int $index, bool $expected ): void
	{
		$list = new FloatTypeList( ...$values );

		self::assertSame( $expected, $list->exists( $index ) );
	}

	public function testGet(): void
	{
		$list = new FloatTypeList(
			new FloatType( 1.1 ),
			new FloatType( 2.2 ),
			new FloatType( 3.3 ),
			new FloatType( 4.4 ),
		);

		self::assertEquals( new FloatType( 4.4 ), $list->get( 3 ) );
	}

	public function testGetThrowsExceptionIfIndexNotExists(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new FloatTypeList( new FloatType( 1.1 ) );
		$list->get( 42 );
	}

	public function testToJson(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );

		$json    = $list->toJson();
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 1.1, 2.2 ], $decoded );
	}

	public function testToJsonPretty(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );

		$json = $list->toJson( true );

		self::assertStringContainsString( "\n", $json );
		self::assertSame( [ 1.1, 2.2 ], json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function testJsonSerialize(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );

		$json    = json_encode( $list );
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ 1.1, 2.2 ], $decoded );
	}

	public function testFirst(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );

		self::assertEquals( new FloatType( 1.1 ), $list->first() );
	}

	public function testLast(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );

		self::assertEquals( new FloatType( 2.2 ), $list->last() );
	}

	public function testFindIndex(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );

		$matcher = new class implements MatchesType {
			public function matches( RepresentsType $type ): bool
			{
				return $type->toNativeType() === 2.2;
			}
		};

		self::assertSame( 1, $list->findIndex( $matcher ) );
	}

	public function testFindIndexReturnsNullIfNothingMatched(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ) );

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
			new InvalidFloatTypeListException( 'Invalid ' . NoTwoFloatTypeList::class )
		);

		new NoTwoFloatTypeList( new FloatType( 2.0 ) );
	}

	public function testToNativeType(): void
	{
		$type1 = new FloatType( 1.1 );
		$type2 = new FloatType( 2.2 );
		$list  = new FloatTypeList( $type1, $type2 );

		self::assertSame( [ $type1, $type2 ], $list->toNativeType() );
	}

	public function testToFloats(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );

		self::assertSame( [ 1.1, 2.2 ], $list->toFloats() );
	}

	public function testIterateFloats(): void
	{
		$list = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );

		self::assertSame( [ 1.1, 2.2 ], iterator_to_array( $list->iterateFloats() ) );
	}
}
