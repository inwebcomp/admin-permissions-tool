<?php

namespace Inweb\Tools\PermissionsTool\Http\Controllers;

use Illuminate\Routing\Controller;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\HasPermissions;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use InWeb\Admin\App\Http\Requests\ResourceDeleteRequest;
use InWeb\Admin\App\Http\Requests\ResourceStoreRequest;
use InWeb\Admin\App\Http\Requests\ResourceUpdateRequest;
use InWeb\Admin\App\Models\AdminUser;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    public function index(AdminRequest $request)
    {
        $sections = array_merge(
            Admin::$resources,
            Admin::$tools
        );

        return Role::all()->filter(function (Role $role) {
            return $role->name != 'Super Admin';
        })->map(function (Role $role) use ($sections, $request) {
            return [
                'id'        => $role->id,
                'title'     => $role->name,
                'resources' => collect($sections)->filter(function ($section) {
                    return in_array(HasPermissions::class, class_uses_recursive($section));
                })->map(function ($resource) use ($role) {
                    $permissions = $role->permissions->pluck('name')->toArray();
                    $allPermissions = Permission::all(['name'])->pluck('name')->toArray();
                    $resourcePermissions = collect($resource::permissionActions())
                        ->filter(function ($title, $action) use ($allPermissions, $resource) {
                            return in_array($resource::permissionActionName($action), $allPermissions);
                        });

                    return [
                        'title'       => $resource::label(),
                        'uid'         => $key = $resource::uriKey(),
                        'permissions' =>
                            collect($resourcePermissions)->map(function ($title, $action) use ($resource, $permissions, $key) {
                                return [
                                    'title'     => $title,
                                    'action'    => $action,
                                    'permitted' => in_array($resource::permissionActionName($action), $permissions)
                                ];
                            })->keyBy('action')
                    ];
                })->filter(function($resource) {
                    return count($resource['permissions']);
                })->keyBy('uid')
            ];
        })->keyBy('id');
    }

    public function update(AdminRequest $request, Role $role, $resource, $action)
    {
        $permitted = (bool) $request->input('value');

        $section = Admin::resourceForKey($resource);

        if (! $section)
            $section = Admin::toolForKey($resource);

        if (! $section)
            return abort(404, 'Section Not Found: ' . $section);

        if (! isset($section::permissionActions()[$action]))
            return abort(404, 'Action Not Found: ' . $action);

        if ($permitted) {
            $role->givePermissionTo($section::permissionActionName($action));
        } else {
            $role->revokePermissionTo($section::permissionActionName($action));
        }

        return [
            'permitted' => $permitted
        ];
    }

    public function updateAll(AdminRequest $request, Role $role, $resource)
    {
        $permitted = (bool) $request->input('value');

        $section = Admin::resourceForKey($resource);

        if (! $section)
            $section = Admin::toolForKey($resource);

        if (! $section)
            return abort(404, 'Section Not Found: ' . $section);

        foreach ($section::permissionActions() as $action => $title) {
            if ($permitted) {
                $role->givePermissionTo($section::permissionActionName($action));
            } else {
                $role->revokePermissionTo($section::permissionActionName($action));
            }
        }

        return [
            'permitted' => $permitted
        ];
    }

    public function updateAllForRole(AdminRequest $request, Role $role)
    {
        $permitted = (bool) $request->input('value');

        $sections = array_merge(
            Admin::$resources,
            Admin::$tools
        );

        $sections = array_filter($sections, function ($section) {
            return in_array(HasPermissions::class, class_uses_recursive($section));
        });

        foreach ($sections as $section) {
            foreach ($section::permissionActions() as $action => $title) {
                if ($permitted) {
                    $role->givePermissionTo($section::permissionActionName($action));
                } else {
                    $role->revokePermissionTo($section::permissionActionName($action));
                }
            }
        }

        return [
            'permitted' => $permitted
        ];
    }

    public function roles()
    {
        return Role::all()->map(function (Role $role) {
            return [
                'title' => $role->name,
                'value' => $role->id,
            ];
        });
    }

    public function userRoles(AdminRequest $request)
    {
        return $request->user()->fresh()->roles->map(function (Role $role) {
            return [
                'title' => $role->name,
                'id'    => $role->id,
            ];
        });
    }

    public function assignRoleToUser(ResourceUpdateRequest $request)
    {
        /** @var AdminUser $model */
        $model = $request->findModelOrFail();

        $role = Role::findById($request->input('role'), 'admin');

        $model->assignRole($role);

        return $this->userRoles($request);
    }

    public function removeRoleFromUser(ResourceUpdateRequest $request)
    {
        /** @var AdminUser $model */
        $model = $request->findModelOrFail();

        $role = Role::findById($request->input('role'), 'admin');

        $model->removeRole($role);

        return $this->userRoles($request);
    }

    public function storeRole(ResourceStoreRequest $request)
    {
        if (! $role = $request->input('role'))
            abort(429, 'Role name is required');

        return Role::findOrCreate($role);
    }

    public function removeRole(ResourceDeleteRequest $request, Role $role)
    {
        if ($role->name == 'Super Admin')
            abort(403, "You can't delete Super Admin role");

        $role->delete();
    }
}
