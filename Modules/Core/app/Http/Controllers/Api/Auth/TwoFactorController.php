<?php

namespace Modules\Core\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Models\User;

class TwoFactorController extends Controller
{
    protected User $user;

    public function __construct(Request $request)
    {
        $this->user = $request->user();
    }

    public function enable()
    {
        // ...
    }
}
