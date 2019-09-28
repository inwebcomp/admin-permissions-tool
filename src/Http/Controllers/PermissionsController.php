<?php

namespace Inweb\Tools\PermissionsTool\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    public function index(AdminRequest $request)
    {
        return Role::all()->map(function (Role $role) use ($request) {
            return [
                'id'        => $role->id,
                'title'     => Str::title($role->name),
                'resources' => collect(Admin::availableResources($request))->map(function ($resource) use ($role) {
                    $permissions = $role->permissions->pluck('name')->toArray();

                    return [
                        'title'       => $resource::label(),
                        'uid'         => $key = $resource::uriKey(),
                        'permissions' => collect($resource::permissionActions())
                            ->map(function ($title, $action) use ($resource, $permissions, $key) {
                                return [
                                    'title'     => $title,
                                    'action'    => $action,
                                    'permitted' => in_array($resource::permissionActionName($action), $permissions)
                                ];
                            })->keyBy('action')
                    ];
                })->keyBy('uid')
            ];
        })->keyBy('id');
    }

    public function update(AdminRequest $request, Role $role, $resource, $action)
    {
        $permitted = (bool) $request->input('value');

        $resource = $request->resource();

        if (! $resource)
            return abort(404, 'Resource Not Found: ' . $resource);

        if (! isset($resource::permissionActions()[$action]))
            return abort(404, 'Action Not Found: ' . $action);

        if ($permitted) {
            $role->givePermissionTo($resource::permissionActionName($action));
        } else {
            $role->revokePermissionTo($resource::permissionActionName($action));
        }

        return [
            'permitted' => $permitted
        ];
    }
}
