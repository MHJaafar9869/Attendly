<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Modules\Core\Traits\ResponseJson;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ResponseJson;
    use ValidatesRequests;

    public function __construct()
    {
        // code...
    }
}
