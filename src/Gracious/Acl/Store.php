<?php

namespace Gracious\Acl;

abstract class Store
{
	/**
	 *
	 * @var AclManager
	 */
	protected $manager;
	public function __construct($aclManager) {
		$this->manager = $aclManager;
	}
	
	abstract function load($id);
}