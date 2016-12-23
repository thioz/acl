<?php
namespace Gracious\Acl;\PermissionFilter;

use App\Acl\PermissionFilter;
use App\User;

class ClosureFilter extends PermissionFilter{
	/**
	 *
	 * @var \Closure
	 */
	protected $closure;
	public function __construct($c) {
		$this->closure=$c;
	}
	
	public function check(User $user, $context = null) {
		return call_user_func($this->closure,$user,$context);
		
	}
	
	function toJson(){
		return serialize($this->closure);
	}

}
