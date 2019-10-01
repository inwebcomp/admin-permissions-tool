<?php

namespace Inweb\Tools\PermissionsTool;

use Illuminate\Http\Request;
use InWeb\Admin\App\ResourceTool;

class RolesResourceTool extends ResourceTool
{
    public $component = 'roles-resource-tool';

    public function name()
    {
        return __('Роли');
    }

    public function authorizedToSee(Request $request)
    {
        return optional($request->user())->can(PermissionsTool::uriKey() . ':viewAny');
    }
}
