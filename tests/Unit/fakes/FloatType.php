<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\fakes;

use ComponoKit\Types\Interfaces\RepresentsFloat;

class FloatType implements RepresentsFloat
{
	use FakingFloatType;
}
