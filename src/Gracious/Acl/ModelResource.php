<?php
namespace Gracious\Acl;


class ModelResource extends Resource{
	
	protected $name;
	protected $model;
	
	public function __construct($name,$model) {
		parent::__construct($name);
		$this->model=$model;
	}
	
	function getContext($id){
		if(!$id){
			return false;
		}
		return call_user_func([$this->model,'find'],$id);
	}
	
	public function is($item) {
		return get_class($item) == $this->model;
	}
	
}
