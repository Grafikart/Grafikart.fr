<?php

namespace App\Tests;

use App\Component\ObjectMapper\ObjectMapperInterface;

class DTOTestCase extends KernelTestCase
{
    protected function transform(object $input, object|string $output)
    {
        return $this->getContainer()->get(ObjectMapperInterface::class)->map($input, $output);
    }


}
