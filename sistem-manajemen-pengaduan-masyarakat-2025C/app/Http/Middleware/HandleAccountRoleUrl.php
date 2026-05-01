<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class HandleAccountRoleUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if ($user) {
            $expectedAccount = Str::slug($user->name);
            $expectedRole = strtolower($user->role);

            // Set defaults for route() helper globally if user is logged in
            URL::defaults([
                'account' => $expectedAccount,
                'role' => $expectedRole,
            ]);

            $account = $request->route('account');
            $role = $request->route('role');

            // If parameters are present in the URL, validate them to ensure consistency
            if ($account && $role) {
                if ($account !== $expectedAccount || $role !== $expectedRole) {
                    // If there's a mismatch, redirect to the canonical URL for this user
                    $routeName = $request->route()->getName();
                    $routeParams = $request->route()->parameters();
                    $routeParams['account'] = $expectedAccount;
                    $routeParams['role'] = $expectedRole;

                    return redirect()->route($routeName, $routeParams);
                }
            }
        }

        return $next($request);
    }
}
