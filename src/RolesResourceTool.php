<?php

namespace Inweb\Tools\PermissionsTool;

use App\Models\Category;
use InWeb\Admin\App\ResourceTool;

class RolesResourceTool extends ResourceTool
{
    public $component = 'roles-resource-tool';

    public function name()
    {
        return __('Роли');
    }
}
