<?php
namespace Gracious\Acl;

class PermissionRequest {

	/**
	 * @var string
	 */
	public $resource;

	/**
	 * @var string
	 */
	public $perm;

	/**
	 * @var int|string
	 */
	public $contextId;
	
	public function __construct($resource,$perm,$contextId) {

		$this->resource=$resource;
		$this->perm=$perm;
		$this->contextId=$contextId;
	}
}
