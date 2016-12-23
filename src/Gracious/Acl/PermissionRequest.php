<?php
namespace Gracious\Acl;

class PermissionRequest {
	public $resource;
	public $perm;
	public $contextId;
	
	public function __construct($resource,$perm,$contextId) {
		$this->resource=$resource;
		$this->perm=$perm;
		$this->contextId=$contextId;
	}
}
