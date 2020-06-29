<?php

namespace AcMarche\Duobac;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcMarcheDuobacBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
