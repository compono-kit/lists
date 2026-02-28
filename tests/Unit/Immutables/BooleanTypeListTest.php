<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables;

use ComponoKit\Types\BooleanType;
use ComponoKit\Types\Factories\BooleanFactory;
use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Immutables\BooleanTypeList;
use ComponoKit\Types\Lists\Immutables\BooleanValueList;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use PHPUnit\Framework\TestCase;

class BooleanTypeListTest extends TestCase
{
	public static function equalsProvider(): iterable
	{
		$type1 = new BooleanType( true );
		$type2 = new BooleanType( false );

		yield 'same order' => [
			[ $type1, $type2 ],
			[ $type1, $type2 ],
			true,
		];

		yield 'different order' => [
			[ $type1, $type2 ],
			[ $type2, $type1 ],
			false,
		];

		yield 'different count' => [
			[ $type1 ],
			[ $type1, $type2 ],
			false,
		];
	}

	/**
	 * @dataProvider equalsProvider
	 */
	public function testEquals( array $types1, array $types2, bool $expected ): void
	{
		$list1 = new BooleanTypeList( ...$types1 );
		$list2 = new BooleanTypeList( ...$types2 );

		self::assertSame( $expected, $list1->equals( $list2 ) );
	}

	public function testRemove(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->remove( 0 );

		self::assertNotSame( $list1, $list2 );
		self::assertCount( 2, $list1 );
		self::assertCount( 1, $list2 );
	}

	public function testAppend(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->append( new BooleanType( true ) );

		self::assertNotSame( $list1, $list2 );
		self::assertCount( 2, $list1 );
		self::assertCount( 3, $list2 );

		self::assertEquals( new BooleanType( true ), $list2->get( 0 ) );
		self::assertEquals( new BooleanType( false ), $list2->get( 1 ) );
		self::assertEquals( new BooleanType( true ), $list2->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->prepend( new BooleanType( true ) );

		self::assertNotSame( $list1, $list2 );
		self::assertCount( 2, $list1 );
		self::assertCount( 3, $list2 );

		self::assertEquals( new BooleanType( true ), $list2->get( 0 ) );
		self::assertEquals( new BooleanType( true ), $list2->get( 1 ) );
		self::assertEquals( new BooleanType( false ), $list2->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->appendList( new BooleanTypeList( new BooleanType( true ) ) );

		self::assertNotSame( $list1, $list2 );
		self::assertCount( 2, $list1 );
		self::assertCount( 3, $list2 );

		self::assertEquals( new BooleanType( true ), $list2->get( 0 ) );
		self::assertEquals( new BooleanType( false ), $list2->get( 1 ) );
		self::assertEquals( new BooleanType( true ), $list2->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->prependList( new BooleanTypeList( new BooleanType( true ) ) );

		self::assertNotSame( $list1, $list2 );
		self::assertCount( 2, $list1 );
		self::assertCount( 3, $list2 );

		self::assertEquals( new BooleanType( true ), $list2->get( 0 ) );
		self::assertEquals( new BooleanType( true ), $list2->get( 1 ) );
		self::assertEquals( new BooleanType( false ), $list2->get( 2 ) );
	}

	public function testUnique(): void
	{
		$list   = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ), new BooleanType( true ) );
		$unique = $list->unique();

		self::assertCount( 2, $unique );
		self::assertEquals( new BooleanType( true ), $list->get( 0 ) );
		self::assertEquals( new BooleanType( false ), $list->get( 1 ) );
	}

	public function testDiff(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->diff( new BooleanTypeList( new BooleanType( false ) ) );

		self::assertCount( 1, $list2 );
		self::assertEquals( new BooleanType( true ), $list2->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->intersect( new BooleanTypeList( new BooleanType( false ) ) );

		self::assertCount( 1, $list2 );
		self::assertEquals( new BooleanType( false ), $list2->get( 0 ) );
	}

	public function testSplit(): void
	{
		$list   = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ), new BooleanType( true ) );
		$chunks = iterator_to_array( $list->split( 2 ) );

		self::assertCount( 2, $chunks );
		self::assertCount( 2, $chunks[0] );
		self::assertCount( 1, $chunks[1] );
	}

	public function testSlice(): void
	{
		$list  = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ), new BooleanType( true ) );
		$slice = $list->slice( 1, 1 );

		self::assertCount( 1, $slice );
		self::assertEquals( new BooleanTypeList( new BooleanType( false ) ), $slice );
	}

	public function testShuffle(): void
	{
		$list     = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$shuffled = $list->shuffle();

		self::assertCount( 2, $shuffled );

		$values = $shuffled->toBooleans();
		sort( $values );

		self::assertSame( [ false, true ], $values );
	}

	/* ---------------------------------
	 * sort
	 * --------------------------------- */

	public function testSort(): void
	{
		$type1 = new BooleanType( true );
		$type2 = new BooleanType( false );

		$list1 = new BooleanTypeList(  $type1, $type2  );

		$comparer = new class implements ComparesTypes {
			public function compare( RepresentsType $firstType, RepresentsType $secondType ): int
			{
				return $firstType->toNativeType() <=> $secondType->toNativeType();
			}
		};

		$list2 = $list1->sort( $comparer );

		self::assertSame( [ false, true ], $list2->toBooleans() );
	}

	/* ---------------------------------
	 * reverse
	 * --------------------------------- */

	public function testReverse(): void
	{
		$type1 = new BooleanType( true );
		$type2 = new BooleanType( false );

		$list1 = new BooleanTypeList(  $type1, $type2  );
		$list2 = $list1->reverse();

		self::assertSame( [ false, true ], $list2->toBooleans() );
	}

	/* ---------------------------------
	 * replace
	 * --------------------------------- */

	/**
	 * @dataProvider replaceProvider
	 */
	public function testReplace( array $values, int $index, bool $expected ): void
	{
		$list1 = new BooleanTypeList( ...$values );
		$type1 = new BooleanType( $expected );

		$list2 = $list1->replace( $index, $type1 );

		self::assertSame( $expected, $list2->get( $index )->toBoolean() );
	}

	public static function replaceProvider(): iterable
	{
		yield 'replace first' => [
			[ new BooleanType( false ), new BooleanType( true ) ],
			0,
			true,
		];

		yield 'replace second' => [
			[ new BooleanType( true ), new BooleanType( false ) ],
			1,
			true,
		];
	}

	public function testReplaceThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list1 = new BooleanTypeList(  new BooleanType( true ) );
		$list1->replace( 99, new BooleanType( false ) );
	}

	/* ---------------------------------
	 * exists / get
	 * --------------------------------- */

	/**
	 * @dataProvider existsProvider
	 */
	public function testExists( array $values, int $index, bool $expected ): void
	{
		$list1 = new BooleanTypeList( ...$values );

		self::assertSame( $expected, $list1->exists( $index ) );
	}

	public static function existsProvider(): iterable
	{
		yield 'index exists' => [
			[ new BooleanType( true ) ],
			0,
			true,
		];

		yield 'index does not exist' => [
			[ new BooleanType( true ) ],
			1,
			false,
		];
	}

	public function testGetThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list1 = new BooleanTypeList(  new BooleanType( true )  );
		$list1->get( 42 );
	}

	/* ---------------------------------
	 * fromJson / toJson / jsonSerialize
	 * --------------------------------- */

	public function testFromJson(): void
	{
		$expectedList = new BooleanTypeList( new BooleanType( false ), new BooleanType( true ) );

		$list1 = BooleanTypeList::fromValueList( new BooleanValueList(false, true), new BooleanFactory() );

		self::assertEquals( $expectedList, $list1);
	}

	/**
	 * @dataProvider toJsonProvider
	 */
	public function testToJson( bool $pretty ): void
	{
		$list1 = new BooleanTypeList( 			new BooleanType( true ),			new BooleanType( false )		);

		$json    = $list1->toJson( $pretty );
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ true, false ], $decoded );
	}

	public static function toJsonProvider(): iterable
	{
		yield 'compact json' => [ false ];
		yield 'pretty json' => [ true ];
	}

	public function testJsonSerialize(): void
	{
		$list1 = new BooleanTypeList( 			new BooleanType( true ),			new BooleanType( false ), );

		self::assertSame(
			[ true, false ],
			array_map(
				static fn( RepresentsBoolean $type ): bool => $type->toBoolean(),
				$list1->jsonSerialize()
			)
		);
	}

	/* ---------------------------------
	 * first / last
	 * --------------------------------- */

	public function testFirstAndLast(): void
	{
		$type1 = new BooleanType( true );
		$type2 = new BooleanType( false );

		$list1 = new BooleanTypeList(  $type1, $type2  );

		self::assertTrue( $list1->first()->toBoolean() );
		self::assertFalse( $list1->last()->toBoolean() );
	}

	/* ---------------------------------
	 * findIndex
	 * --------------------------------- */

	public function testFindIndex(): void
	{
		$type1 = new BooleanType( true );
		$type2 = new BooleanType( false );

		$list1 = new BooleanTypeList(  $type1, $type2  );

		$matcher = new class implements MatchesType {
			public function matches( RepresentsType $type ): bool
			{
				return $type->toNativeType() === false;
			}
		};

		self::assertSame( 1, $list1->findIndex( $matcher ) );
	}

	public function testFindIndexReturnsNull(): void
	{
		$list1 = new BooleanTypeList(  new BooleanType( true )  );

		$matcher = new class implements MatchesType {
			public function matches( RepresentsType $type ): bool
			{
				return false;
			}
		};

		self::assertNull( $list1->findIndex( $matcher ) );
	}

	/* ---------------------------------
	 * equalsValues
	 * --------------------------------- */

	/**
	 * @dataProvider equalsValuesProvider
	 */
	public function testEqualsValues( array $values1, array $values2, bool $expected ): void
	{
		$list1 = new BooleanTypeList( ...$values1 );
		$list2 = new BooleanTypeList( ...$values2 );

		self::assertSame( $expected, $list1->equalsValues( $list2 ) );
	}

	public static function equalsValuesProvider(): iterable
	{
		yield 'same values same order' => [
			[ new BooleanType( true ), new BooleanType( false ) ],
			[ new BooleanType( true ), new BooleanType( false ) ],
			true,
		];

		yield 'same values different order' => [
			[ new BooleanType( true ), new BooleanType( false ) ],
			[ new BooleanType( false ), new BooleanType( true ) ],
			false,
		];

		yield 'different values' => [
			[ new BooleanType( true ) ],
			[ new BooleanType( false ) ],
			false,
		];
	}
}
