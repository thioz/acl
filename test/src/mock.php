<?php

class MockUser implements \Gracious\Acl\AclIdentityInterface {
	protected $roleId;
	protected $id;
	public function __construct($id, $roleId)
	{
		$this->id = $id;
		$this->roleId = $roleId;
	}

	function getRoleId()
	{
		return $this->roleId;
	}

	function getId()
	{
		return $this->id;
	}
}



class MockPostModelRepository{
	protected static $data = [
		1=> ['title'=> 'just a post with id 1','user_id'=> 1],
		2=> ['title'=> 'just a post with id 2','user_id'=> 2],
		3=> ['title'=> 'just a post with id 3','user_id'=> 1],
	];

	public static function get($id){
		return static::$data[$id];
	}


}


abstract class MockModelResource{
	protected $data = [];
	public function __construct($data)
	{
		$this->data =$data;
	}

	public function getOwner(){
		return $this->data['user_id'];
	}

	abstract static function find($id);
}

class MockPost extends MockModelResource {

	static function find($id)
	{
		return new static(MockPostModelRepository::get($id));
	}
}

class ModelOwnerFilter extends Gracious\Acl\PermissionFilter {

	function check(\Gracious\Acl\AclIdentityInterface $identity, $context = null)
	{
		if($context){
			return $context->getOwner() == $identity->getId();
		}
	}
}