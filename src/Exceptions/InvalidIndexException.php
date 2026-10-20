<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Exceptions;

use ComponoKit\Types\Lists\Interfaces\RepresentsListTypeException;
use ComponoKit\Types\Interfaces\Traits\ProvidingContext;

class InvalidIndexException extends \LogicException implements RepresentsListTypeException
{
	use ProvidingContext;
}
