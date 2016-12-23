<?php
namespace Gracious\Acl;

use App\User;

abstract  class PermissionFilter {
	
	abstract function check(User $user, $context = null);
}
