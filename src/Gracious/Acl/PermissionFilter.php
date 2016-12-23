<?php
namespace Gracious\Acl;

abstract  class PermissionFilter {
	
	abstract function check(AclIdentityInterface $identity, $context = null);
}
