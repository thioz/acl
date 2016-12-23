<?php

namespace Gracious\Acl;

use App\User;

class Resource {

	protected $name;

	public function __construct($name) {
		$this->name = $name;
	}

	function getName() {
		return $this->name;
	}

	function getContext($id) {
		return false;
	}

	function getContextId($res) {
		return false;
	}

	function getPermission($item) {
		return false;
	}

	function is($item) {
		return (is_string($item) && $item == $this->name) ? true : false;
	}

}
