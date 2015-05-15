<?php namespace drpdigital\Middleware;

use Request;
use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckForMaintenanceModeWithWhiteList implements Middleware {

	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function handle($request, Closure $next)
	{
		if ($this->app->isDownForMaintenance() && !$this->ipIsWhiteListed())
		{
			throw new HttpException(503);
		}

		return $next($request);
	}

    private function ipIsWhiteListed()
    {
        $ip = Request::getClientIp();

        $allowed = explode(',', env('MAINTENANCE_WHITELIST'));

        return in_array($ip, $allowed);
    }

}

