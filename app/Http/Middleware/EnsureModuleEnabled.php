<?php

namespace App\Http\Middleware;

use App\Support\Crm;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        abort_unless(Crm::moduleEnabled($module), 404);

        return $next($request);
    }
}
