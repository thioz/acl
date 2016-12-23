<?php
namespace Gracious\Acl\Middleware;

use Closure;

class RouteGuard{
	
    public function handle($request, Closure $next, $guard = null)
    {
			$config = \Config::get('acl.routes');

			foreach($config as $path=>$pathconfig){
				if($request->is($path)){
					$user=\App\User::find(1);
					$id = $request->id;
					$res=$pathconfig['resource'];
					$perm=$pathconfig['permission'];
							$allowed = \Acl::isAllowed($user, $res,$perm,$id);
		var_dump($allowed);
				}
			}
		 return $next($request);
    }	
}