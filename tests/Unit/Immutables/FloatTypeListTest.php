<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables;

use ComponoKit\Types\Factories\FloatFactory;
use ComponoKit\Types\FloatType;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\InvalidFloatTypeListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Immutables\FloatTypeList;
use ComponoKit\Types\Lists\Immutables\FloatValueList;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes\NoTwoFloatTypeList;
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
	public function testEquals( RepresentsImmutableFloatTypeList $originalList, RepresentsImmutableFloatTypeList $actualList, bool $expected ): void
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
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$actualList   = $originalList->remove( 0 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 1, $actualList );
	}

	public function testAppend(): void
	{
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$actualList   = $originalList->append( new FloatType( 1.1 ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new FloatType( 1.1 ), $actualList->get( 0 ) );
		self::assertEquals( new FloatType( 2.2 ), $actualList->get( 1 ) );
		self::assertEquals( new FloatType( 1.1 ), $actualList->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$actualList   = $originalList->prepend( new FloatType( 1.1 ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new FloatType( 1.1 ), $actualList->get( 0 ) );
		self::assertEquals( new FloatType( 1.1 ), $actualList->get( 1 ) );
		self::assertEquals( new FloatType( 2.2 ), $actualList->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$actualList   = $originalList->appendList( new FloatTypeList( new FloatType( 1.1 ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new FloatType( 1.1 ), $actualList->get( 0 ) );
		self::assertEquals( new FloatType( 2.2 ), $actualList->get( 1 ) );
		self::assertEquals( new FloatType( 1.1 ), $actualList->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$actualList   = $originalList->prependList( new FloatTypeList( new FloatType( 1.1 ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $originalList );
		self::assertCount( 3, $actualList );

		self::assertEquals( new FloatType( 1.1 ), $actualList->get( 0 ) );
		self::assertEquals( new FloatType( 1.1 ), $actualList->get( 1 ) );
		self::assertEquals( new FloatType( 2.2 ), $actualList->get( 2 ) );
	}

	public function testUnique(): void
	{
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ), new FloatType( 1.1 ) );
		$actualList   = $originalList->unique();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertEquals( new FloatType( 1.1 ), $originalList->get( 0 ) );
		self::assertEquals( new FloatType( 2.2 ), $originalList->get( 1 ) );
	}

	public function testDiff(): void
	{
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$actualList   = $originalList->diff( new FloatTypeList( new FloatType( 2.2 ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new FloatType( 1.1 ), $actualList->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$actualList   = $originalList->intersect( new FloatTypeList( new FloatType( 2.2 ) ) );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new FloatType( 2.2 ), $actualList->get( 0 ) );
	}

	public function testSplit(): void
	{
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ), new FloatType( 3.3 ) );
		$chunkedList  = iterator_to_array( $originalList->split( 2 ) );

		self::assertCount( 3, $originalList );
		self::assertCount( 2, $chunkedList );
		self::assertEquals( new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) ), $chunkedList[0] );
		self::assertEquals( new FloatTypeList( new FloatType( 3.3 ) ), $chunkedList[1] );
	}

	public function testSlice(): void
	{
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ), new FloatType( 3.3 ) );
		$actualList   = $originalList->slice( 1, 1 );

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 1, $actualList );
		self::assertEquals( new FloatTypeList( new FloatType( 2.2 ) ), $actualList );
	}

	public function testShuffle(): void
	{
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) );
		$actualList   = $originalList->shuffle();

		self::assertNotSame( $originalList, $actualList );
		self::assertCount( 2, $actualList );
		self::assertEqualsCanonicalizing(
			new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ) ),
			$actualList
		);
	}

	public function testSort(): void
	{
		$originalList = new FloatTypeList( new FloatType( 3.3 ), new FloatType( 1.1 ) );

		$comparer = new class implements ComparesTypes {
			public function compare( RepresentsType $firstType, RepresentsType $secondType ): int
			{
				return $firstType->toNativeType() <=> $secondType->toNativeType();
			}
		};

		$actualList = $originalList->sort( $comparer );

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals(
			new FloatTypeList( new FloatType( 1.1 ), new FloatType( 3.3 ) ),
			$actualList
		);
	}

	public function testReverse(): void
	{
		$originalList = new FloatTypeList( new FloatType( 1.1 ), new FloatType( 2.2 ), new FloatType( 3.3 ) );
		$actualList   = $originalList->reverse();

		self::assertNotSame( $originalList, $actualList );
		self::assertEquals(
			new FloatTypeList( new FloatType( 3.3 ), new FloatType( 2.2 ), new FloatType( 1.1 ) ),
			$actualList
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
		$originalList = new FloatTypeList( ...$values );
		$type1        = new FloatType( $expected );

		$actualList = $originalList->replace( $index, $type1 );

		self::assertNotSame( $originalList, $actualList );
		self::assertSame( $expected, $actualList->get( $index )->toFloat() );
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

	public function testFromValueList(): void
	{
		$list         = FloatTypeList::fromValueList( new FloatValueList( 2.2, 1.1 ), new FloatFactory() );
		$expectedList = new FloatTypeList( new FloatType( 2.2 ), new FloatType( 1.1 ) );

		self::assertEquals( $expectedList, $list );
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
			new InvalidFloatTypeListException( 'Invalid value in ' . NoTwoFloatTypeList::class )
		);

		new NoTwoFloatTypeList( new FloatType( 2.0 ) );
	}

	public function testThrowsExceptionIfKeysAreUsed(): void
	{
		$this->expectExceptionObject(
			new InvalidFloatTypeListException( 'Invalid index in ' . FloatTypeList::class )
		);

		new FloatTypeList( ...[ 'foo' => new FloatType( 3.0 ) ] );
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
