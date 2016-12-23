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
		
		$this->app->bind('acl.store.json', function($app){
			return new \Gracious\Acl\Store\JsonStore($app['acl.manager']);
		});
		
		$this->registerPermissionFilters();
		
		
		
	}

	function registerPermissionFilters() {
		$this->app->bind('acl.permission.owner', \Gracious\Acl\PermissionFilter\Owner::class);
		$this->app->bind('acl.permission.account', \Gracious\Acl\PermissionFilter\PartOfAccount::class);
	}

}
