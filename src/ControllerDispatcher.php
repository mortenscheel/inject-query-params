<?php

namespace Scheel\InjectQueryParams;

use Scheel\InjectQueryParams\Concerns\InjectsQueryParamsFromAttribute;

class ControllerDispatcher extends \Illuminate\Routing\ControllerDispatcher
{
    use InjectsQueryParamsFromAttribute;
}
