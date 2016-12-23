<?php

namespace Gracious\Acl;


class Role {

	protected $id;
	protected $description;

	public function __construct($id, $description = null) {
		$this->id = $id;
		$this->description = $description;
	}

	function getId() {
		return $this->id;
	}

	function setId($id) {
		$this->id = $id;
		return $this;
	}
	function getDescription() {
		return $this->description;
	}

	function setDescription($description) {
		$this->description = $description;
		return $this;
	}



}
