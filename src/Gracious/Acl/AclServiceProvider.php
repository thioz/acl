<?php

namespace Gracious\Acl;

use Illuminate\Support\ServiceProvider;

class AclServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		//
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->singleton('acl.manager', function($app){
			return new \Gracious\Acl\AclManager();
		});
		
 
		
		
		
	}
 

}
