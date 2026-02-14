<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Exceptions;

use ComponoKit\Types\Interfaces\RepresentsTypeException;
use ComponoKit\Types\Interfaces\Traits\ProvidingContext;

class InvalidStringValueListException extends \LogicException implements RepresentsTypeException
{
	use ProvidingContext;
}
