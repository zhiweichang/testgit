<?php

namespace App\Http\Controllers;

use App\Http\Responses\AppResponseInJson;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AppResponseInJson;
}
