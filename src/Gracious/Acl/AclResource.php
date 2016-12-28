<?php

namespace Gracious\Acl;


class AclResource {

	/**
	 * @var string
	 */
	protected $name;

	public function __construct($name) {
		$this->name = $name;
	}

	function getName() {
		return $this->name;
	}

	/**
	 * @param $id integer
	 * @return bool
	 */
	function getContext($id) {
		return false;
	}

	/**
	 * @param $res
	 * @return bool
	 */
	function getContextId($res) {
		return false;
	}

	function is($item) {
		return (is_string($item) && $item == $this->name) ? true : false;
	}

}
