# ComponoKit / Lists

**Strongly typed list objects for PHP 8.5+**

This package provides a lightweight foundation for building *domain-driven*, *immutable*, and *type-safe* list objects in PHP.

## Requirements

* PHP 8.5+
* compono-kit/lists-interfaces

## Concepts

### TypeList vs. ValueList

A **ValueList** holds native PHP scalars directly:

```php
use ComponoKit\Types\Lists\Immutables\StringValueList;

$tags = new StringValueList('php', 'clean-code', 'ddd');
$tags->get(0); // 'php'
```

A **TypeList** holds objects that implement a `RepresentsType` interface (from `compono-kit/types`):

```php
use ComponoKit\Types\Lists\Immutables\StringTypeList;

$tags = new StringTypeList(
    new StringType('php'),
    new StringType('clean-code'),
);
$tags->get(0)->toString(); // 'php'
```

### Immutable vs. Mutable

**Immutable** lists never change — operations return a new list:

```php
use ComponoKit\Types\Lists\Immutables\IntegerValueList;

$numbers = new IntegerValueList(1, 2, 3);
$more    = $numbers->append(4, 5);

$numbers->count(); // 3 — unchanged
$more->count();    // 5
```

**Mutable** lists modify in place and return `void`:

```php
use ComponoKit\Types\Lists\Mutables\IntegerValueList;

$numbers = new IntegerValueList(1, 2, 3);
$numbers->append(4, 5);

$numbers->count(); // 5
```

## Basic Usage

### Creating a list

All lists accept a variadic list of items in their constructor:

```php
$prices  = new FloatValueList(9.99, 14.99, 4.50);
$enabled = new BooleanValueList(true, false, true);
$scores  = new IntegerValueList(42, 17, 99);
```

### Accessing items

```php
$list = new StringValueList('a', 'b', 'c');

$list->count();        // 3
$list->first();        // 'a'
$list->last();         // 'c'
$list->get(1);         // 'b'
$list->exists(5);      // false
```

### Iterating

Lists implement `Iterator`, so you can use them in a `foreach` directly:

```php
foreach ($list as $index => $value) {
    echo "$index: $value\n";
}
```

## Operations

### Append & Prepend

```php
$list = new StringValueList('b', 'c');

$list->append('d', 'e');   // ['b', 'c', 'd', 'e']
$list->prepend('a');       // ['a', 'b', 'c', 'd', 'e']
```

You can also merge two lists of the same type:

```php
$first  = new StringValueList('a', 'b');
$second = new StringValueList('c', 'd');

$merged = $first->appendList($second);  // ['a', 'b', 'c', 'd']
```

### Remove & Replace

```php
$list = new IntegerValueList(10, 20, 30);

$list->remove(1);        // [10, 30]
$list->replace(0, 99);   // [99, 30]
```

### Sort, Reverse & Shuffle

```php
$list     = new IntegerValueList(3, 1, 2);
$reversed = $list->reverse();  // [2, 1, 3]
$shuffled = $list->shuffle();  // random order

// Custom sort via a comparer object:
$sorted = $list->sort(new class implements ComparesValues {
    public function compare(mixed $a, mixed $b): int {
        return $a <=> $b;
    }
});
// [1, 2, 3]
```

### Unique, Diff & Intersect

```php
$a = new IntegerValueList(1, 2, 2, 3);
$b = new IntegerValueList(2, 3, 4);

$a->unique();          // [1, 2, 3]
$a->diff($b);          // [1]       — in $a but not in $b
$a->intersect($b);     // [2, 3]    — in both
```

### Slice & Split

```php
$list  = new IntegerValueList(1, 2, 3, 4, 5);

$slice = $list->slice(1, 3);   // [2, 3, 4]

// Split into chunks of 2:
foreach ($list->split(2) as $chunk) {
    // $chunk is an IntegerValueList
}
```

### Search

```php
$list  = new StringValueList('alpha', 'beta', 'gamma');

$index = $list->findIndex(new class implements MatchesValue {
    public function matches(mixed $value): bool {
        return $value === 'beta';
    }
});
// $index === 1
```

### JSON

```php
$list = new StringValueList('foo', 'bar');

$list->toJson();        // '["foo","bar"]'
$list->toJson(true);    // pretty-printed
json_encode($list);     // also works — implements JsonSerializable

$restored = StringValueList::fromJson('["foo","bar"]');
```

## Extending with Custom Validation

Override `isValueValid()` in a concrete subclass to add domain constraints.
Invalid items throw the matching `Invalid*Exception`.

```php
use ComponoKit\Types\Lists\Immutables\AbstractStringValueList;

class EmailList extends AbstractStringValueList
{
    protected static function isValueValid(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}

$emails = new EmailList('a@example.com', 'b@example.com');
$emails = $emails->append('c@example.com');

new EmailList('not-an-email'); // throws InvalidStringValueListException
```

The same pattern works for TypeLists:

```php
use ComponoKit\Types\Lists\Immutables\AbstractStringTypeList;

class NonEmptyStringList extends AbstractStringTypeList
{
    protected static function isValueValid(RepresentsString $type): bool
    {
        return $type->toString() !== '';
    }
}
```

## Available List Types

| Type      | TypeList              | ValueList              |
|-----------|-----------------------|------------------------|
| String    | `StringTypeList`      | `StringValueList`      |
| Integer   | `IntegerTypeList`     | `IntegerValueList`     |
| Float     | `FloatTypeList`       | `FloatValueList`       |
| Boolean   | `BooleanTypeList`     | `BooleanValueList`     |
| Mixed     | `MixedTypeList`       | `MixedValueList`       |

Each type exists in both `Immutables\` and `Mutables\` namespaces.
