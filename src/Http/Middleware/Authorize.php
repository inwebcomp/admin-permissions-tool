<?php

namespace Inweb\Tools\PermissionsTool\Http\Middleware;

use InWeb\Admin\App\Admin;
use Inweb\Tools\PermissionsTool\PermissionsTool;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        $tool = collect(Admin::registeredTools())->first([$this, 'matchesTool']);

        return optional($tool)->authorize($request) ? $next($request) : abort(403);
    }

    /**
     * Determine whether this tool belongs to the package.
     *
     * @param \InWeb\Admin\App\Tool $tool
     * @return bool
     */
    public function matchesTool($tool)
    {
        return $tool instanceof PermissionsTool;
    }
}
