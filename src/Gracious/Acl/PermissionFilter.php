<?php
namespace Gracious\Acl;

abstract  class PermissionFilter {

	/**
	 * @param AclIdentityInterface $identity
	 * @param null $context
	 * @return boolean
	 */
	abstract function check(AclIdentityInterface $identity, $context = null);
}
