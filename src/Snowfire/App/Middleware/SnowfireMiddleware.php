<?php

namespace Snowfire\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use Snowfire\App\Facades\Snowfire;

class SnowfireMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
        if ( ! Snowfire::isRequestFromSnowfire() && ! Config::get('snowfire.debug'))  {
            return Response::make('Please request this url from a Snowfire component', 500);
        }

        $accountsRepository = app()->make('\Snowfire\App\Repositories\AccountsRepository');

        if (Config::get('snowfire.debug')) {

            // In debug mode, use the first account id
            $app = $accountsRepository->first();

        } else {

            // Load Snowfire account based on URL
            parse_str($request->getQueryString(), $query);
            $app = $accountsRepository->getByKey($query['key']);

        }

        app()->make('view')->composer('*', function($view) use ($app)
        {
            $view->snowfire = $app;
        });

        app()->instance('snowfire', $app);

		return $next($request);
	}

}
