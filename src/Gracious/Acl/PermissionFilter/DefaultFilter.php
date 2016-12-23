<?php
namespace Gracious\Acl;\PermissionFilter;

use App\Acl\PermissionFilter;
use App\User;

class DefaultFilter extends PermissionFilter{
	
	public function check(User $user, $context = null) {
		return true;
	}

}
