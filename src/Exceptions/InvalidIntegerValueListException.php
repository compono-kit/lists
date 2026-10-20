<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Exceptions;

use ComponoKit\Types\Lists\Interfaces\RepresentsListTypeException;
use ComponoKit\Types\Interfaces\Traits\ProvidingContext;

class InvalidIntegerValueListException extends \LogicException implements RepresentsListTypeException
{
	use ProvidingContext;
}
