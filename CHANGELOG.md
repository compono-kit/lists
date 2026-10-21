# CHANGELOG

## [dev-php/8.0]

### Changed
* Maintenance branch of 2.0.0 for PHP 8.0, receives fixes only
* Requires PHP 8.0, `compono-kit/lists-interfaces` dev-php/8.0 and for development `compono-kit/types` dev-php/8.0 and PHPUnit 9.6
* `sort()` passes the comparer as callable array instead of first-class callable

## [2.0.0] - 2026-10-14

### Changed
* [BC] Requires PHP 8.5, `compono-kit/lists-interfaces` ^2.0 and for development `compono-kit/types` ^2.0
* [BC] Mutable lists validate values in `append()`, `prepend()`, `replace()`, `appendList()` and `prependList()` with `isValueValid()`
* [BC] `replace()`, `append()`, `prepend()`, `appendList()` and `prependList()` moved from the mutable list traits to the abstract mutable lists
* [BC] `unique()` of mutable type lists compares items with `equals()` like the immutable type lists
* [BC] `isValueValid()` is `protected` in all list classes
* [BC] All exceptions implement `RepresentsListTypeException`
* `diff()` and `intersect()` of mutable lists return `static`
* `sort()` passes the comparer as first-class callable
* The context of validation exceptions always contains `index` and `value`

### Added
* PHPUnit configuration in `build/phpunit.xml` and the composer script `test`

### Fixed
* Iterating lists used `null` as array offset, which is deprecated since PHP 8.5
* `first()`, `last()` and `current()` throw `MissingIndexException` on empty lists or after the last item instead of failing with a type error
* `appendList()` and `prependList()` of immutable value lists failed with a type error when a type list was passed
* `diff()` and `intersect()` could return wrong results, because the comparison callback was no valid ordering
* `unique()` of mutable float lists merged values with the same integer part
* `unique()` of mutable value lists merged values like `1` and `'1'` and failed for arrays in mixed value lists
* `Mutables\AbstractMixedValueList::isValueValid()` accepted only strings
