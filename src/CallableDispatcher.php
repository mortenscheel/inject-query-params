<?php

namespace Scheel\InjectQueryParams;

use Scheel\InjectQueryParams\Concerns\InjectsQueryParamsFromAttribute;

class CallableDispatcher extends \Illuminate\Routing\CallableDispatcher
{
    use InjectsQueryParamsFromAttribute;
}
