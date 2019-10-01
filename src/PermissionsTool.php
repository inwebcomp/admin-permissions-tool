<?php

namespace Inweb\Tools\PermissionsTool;

use Illuminate\Http\Request;
use InWeb\Admin\App\HasPermissions;
use InWeb\Admin\App\Tool;

class PermissionsTool extends Tool
{
    use HasPermissions;

    public static function uriKey()
    {
        return 'permissions-tool';
    }

    public static function label()
    {
        return __('Права доступа');
    }

    public function authorizedToSee(Request $request)
    {
        return optional($request->user())->can(static::uriKey() . ':viewAny');
    }

    public static function permissionActions()
    {
        return [
            'viewAny' => __('Доступ'),
        ];
    }
}