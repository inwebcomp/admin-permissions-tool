<?php

namespace Inweb\Tools\PermissionsTool;

use Illuminate\Http\Request;
use InWeb\Admin\App\Tool;

class PermissionsTool extends Tool
{
    public static function uriKey()
    {
        return 'permissions-tool';
    }

    public static function label()
    {
        return __('Привилегии');
    }

    public function authorizedToSee(Request $request)
    {
        return true;
    }
}