<?php

namespace Gracious\Acl\Store;

use App\Acl\Store;

class JsonStore extends Store {
	public function load($id) {
		$filename = $id;
		if (file_exists($filename)) {
			$data = json_decode(file_get_contents($filename), true);
			$this->build($data);
		}
	}
	
	function save(){
		$rules=$this->manager->rules();
		$doc=[];
		
		$doc['rules']=$this->parseRules($rules);
		$doc['roles']=$this->manager->roles();
		$doc['resources']=$this->manager->resources();
		return $doc;
	}
	
	function parseRules($rules){
		$parsed=[];
		foreach($rules as $roleId=>$roleRules){
			foreach($roleRules as $resourceId=>$resourceRules){
				foreach($resourceRules as $permId => $permRules){
					foreach($permRules as $contextId => $perm){
						foreach($perm['rules'] as $rule){
							$parsed[]=[
								'role_id'=> $roleId,
								'resource_id'=> $resourceId,
								'context_id'=> $contextId,
								'permission_id'=> $permId,
								'flag'=> $rule['flag'],
								'filter'=> $rule['filter'],
							];
						}
					}
				}
			}
		}
		return $parsed;
	}
	
	function buildTree(){
		$rules=$this->manager->rules();
		$parsed=$this->parseRules($rules);
		return $this->parseTree($parsed);
	}
	
	function parseTree($rules){
		$tree=[];
		foreach($rules as $rule){
			$this->parseTreeRole($rule['role_id'],$rule,$tree);
		}
		return $tree;
	}
	
	function parseTreeRole($roleId, $rule,&$tree){
		if(!isset($tree[$roleId])){
			$tree[$roleId]=[];
		}
		$rid = $rule['resource_id'];
		$pid = $rule['permission_id'];
		$cid = $rule['context_id'];
		if(!isset($tree[$roleId][$rid])){
			$tree[$roleId][$rid]=[];
		}
		if(!isset($tree[$roleId][$rid][$pid])){
			$tree[$roleId][$rid][$pid]=[];
		}
		if(!isset($tree[$roleId][$rid][$pid][$cid])){
			$tree[$roleId][$rid][$pid][$cid]=['rules'=>[]];
		}
		$tree[$roleId][$rid][$pid][$cid]['rules'][] = [
			'flag'=>$rule['flag'],
			'filter'=>$rule['filter'],
		];
	}

	function build($data) {
		$this->buildRoles($data['roles']);
		$this->buildResources($data['resources']);
		$this->buildRules($data['rules']);
	}

	function buildRoles($roles) {
		foreach ($roles as $role) {
			$this->manager->addRole($role['id'], $role['name'], $role['inherit']);
		}
	}

	function buildResources($resources) {
		foreach($resources as $resource){
			$this->manager->addResource($resource['name']);
		}
	}

	function buildRules($rules) {
		foreach($rules as $rule){
			
		}
	}

}
