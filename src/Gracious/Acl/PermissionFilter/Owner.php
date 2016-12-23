<?php
namespace Gracious\Acl;\PermissionFilter;

use App\Acl\PermissionFilter;
use App\User;

class Owner extends \Gracious\Acl\PermissionFilter{
	
	public function check(User $user, $context = null) {
		if(!$context){
			return false;
		}
		
		$userId = $user->id;
		return $userId == $context->user_id;
	}

}
