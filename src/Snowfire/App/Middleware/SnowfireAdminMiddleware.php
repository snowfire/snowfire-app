<?php

namespace Snowfire\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Snowfire\App\Facades\Snowfire;

class SnowfireAdminMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
        $id = $request->route()->getParameter('snowfireAppId');
        $accountsRepository = app()->make('\Snowfire\App\Repositories\AccountsRepository');
        $app = $accountsRepository->getById($id);

        if ( ! Snowfire::authorized($id)) {
            if ($app) {
                return Redirect::to($app->site_url . 'a;applications/application/moduleTab/' . Snowfire::parameter('id'));
            } else {
                return Response::make('Not authorized, please login again through Snowfire', 403);
            }
        }

        app()->make('view')->composer('*', function($view) use ($app)
        {
            $view->snowfire = $app;
        });

        app()->instance('snowfire', $app);

		return $next($request);
	}

}
