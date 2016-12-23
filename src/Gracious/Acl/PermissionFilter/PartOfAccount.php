<?php
namespace Gracious\Acl;\PermissionFilter;

use App\Acl\PermissionFilter;
use App\User;

class PartOfAccount extends PermissionFilter{
	
	public function check(User $user, $context = null) {
		if(!$context){
			return false;
		}
		$accountIds= $user->accounts->pluck('id')->toArray();
 
		return in_array($context->account_id, $accountIds);
	}

}
