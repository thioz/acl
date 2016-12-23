<?php

namespace Gracious\Acl;

class AclManager {

	protected $rules = [];
	protected $pres = [];
	protected $resolvers = [];
	protected $roles = [];
	protected $resources = [];
	protected $resourcePermission = [];

	public function __construct() {
	}

	function addRole($id, $name, $inherit = false) {
		$this->roles[$id] = ['id' => $id, 'name' => $name, 'inherit' => $inherit];
		if (!isset($this->rules[$id])) {
			$this->rules[$id] = [];
		}
	}

	function addResource($resource) {

		if (!($resource instanceof \Gracious\Acl\Resource)) {
			$resource = new \Gracious\Acl\Resource($resource);
		}

		$this->resources[$resource->getName()] = $resource;
	}

	function allow($roleId, $resourceId = '*', $permissionId = '*', $contextId = '*', $filter = null) {
		$this->addRule($roleId, $resourceId, $permissionId, $contextId, $filter, true);
	}

	function deny($roleId, $resourceId = '*', $permissionId = '*', $contextId = '*', $filter = null) {
		$this->addRule($roleId, $resourceId, $permissionId, $contextId, $filter, false);
	}

	function rules() {
		return $this->rules;
	}

	function roles() {
		return $this->roles;
	}

	function resources() {
		return $this->resources;
	}

	function addRule($roleId, $resourceId = '*', $permissionId = '*', $contextId = '*', $filter = null, $flag = true) {

		if (!$this->hasResource($resourceId)) {
			$this->addResource($resourceId);
		}

		if (!$permissionId) {
			$permissionId = '*';
		}
		if (!$contextId) {
			$contextId = '*';
		}

		if (!isset($this->rules[$roleId][$resourceId][$permissionId])) {
			$this->rules[$roleId][$resourceId][$permissionId] = [];
		}

		if (!isset($this->rules[$roleId][$resourceId][$permissionId][$contextId])) {
			$this->rules[$roleId][$resourceId][$permissionId][$contextId] = [
				'rules' => []
			];
		}

		$this->rules[$roleId][$resourceId][$permissionId][$contextId]['rules'][] = [
			'flag' => $flag == true ? 'allow' : 'deny',
			'filter' => $filter,
			'filter_id' => $this->makeFilterId($filter)
		];
	}

	function makeFilterId($filter) {
		if (is_object($filter)) {
			return spl_object_hash($filter);
		}
		if (is_string($filter)) {
			return $filter;
		}
	}

	function hasResource($id) {
		return isset($this->resources[$id]);
	}

	function buildFilter($name) {
		if (!is_string($name)) {
			return $name;
		}
		return new $name();
	}

	function getContext($resourceId, $cid) {
		if (!isset($this->resources[$resourceId])) {
			return false;
		}

		$resource = $this->resources[$resourceId];
		return $resource->getContext($cid);
	}

	function getResourceId($item) {
		foreach ($this->resources as $resId => $resource) {
			if ($resource->is($item)) {
				return $resId;
			}
		}
	}

	function makePermissionRequest($resourceId, $permissionId, $contextId) {
		return new PermissionRequest($resourceId, $permissionId, $contextId);
	}

	function resolveRequest($request) {
		foreach ($this->resolvers as $resolver) {
			$resolver->resolve($request);
		}
	}

	function isAllowed(AclIdentityInterface $identity, $resource, $permissionId = false, $contextId = false) {

		$roleId = $identity->getRoleId();
		$rolePerms = $this->getRolePerms($roleId);

		$request = $this->makePermissionRequest($resource, $permissionId, $contextId);

		$this->resolveRequest($request);

		$rules = $this->getPermRules($rolePerms, $request->resource, $request->perm, $request->contextId);
 
		$allow = false;
		$filtered = false;
		$filteredRules = $this->getFilteredRules($rules);

		foreach ($filteredRules as $rule) {
			$filter = $this->buildFilter($rule['filter']);
			$context = $this->getContext($request->resource, $request->contextId);

			if ($filter->check($user, $context)) {
				$allow = $rule['flag'] == 'allow' ? true : false;
				$filtered = true;
			}
		}

		if (!$filtered) {
			foreach ($rules as $perms) {
				foreach ($perms['rules'] as $rule) {
					if (!$rule['filter']) {
						$allow = $rule['flag'] == 'allow' ? true : false;
					}
				}
			}
		}
		return $allow;
	}

	function getRolePerms($roleId) {

		$roleIds = $this->getRoleIds($roleId);
		$perms = [];

		foreach ($roleIds as $rId) {
			$rolePerms = isset($this->rules[$rId]) ? $this->rules[$rId] : [];
			$perms = $this->mergePerms($perms, $rolePerms);
			
		}
		return $perms;
	}

	function mergePerms($org, $merge) {
		foreach ($merge as $resId => $resPerms) {

			foreach ($resPerms as $permId => $perms) {
				foreach ($perms as $contextId => $rules) {
					if (!isset($org[$resId][$permId][$contextId])) {
						$org[$resId][$permId][$contextId]['rules'] = [];
					}
					$org[$resId][$permId][$contextId]['rules'] = $this->mergeRules($org[$resId][$permId][$contextId]['rules'], $rules['rules']);
				}
			}
		}
		return $org;
	}

	function mergeRules($org, $merge) {
		$merged = [];
		$perms = [];
		foreach ([$org, $merge] as $rules) {
			foreach ($rules as $r) {
				$perms[$r['filter_id']] = $r;
			}
		}
		foreach ($perms as $r) {
			$merged[] = $r;
		}

		return $merged;
	}

	function getRoleIds($roleId, &$ids = []) {
		array_unshift($ids, $roleId);
		$role = $this->roles[$roleId];
		if ($role['inherit']) {
			$this->getRoleIds($role['inherit'], $ids);
		}
		return $ids;
	}

	function addResolver($resolver) {
		$this->resolvers[] = $resolver;
	}

	function getFilteredRules($rules) {
		$filtered = [];
		foreach ($rules as $perms) {
			foreach ($perms['rules'] as $rule) {
				if ($rule['filter']) {
					$filtered[] = $rule;
				}
			}
		}
		return $filtered;
	}

	function getPermRules($rolePerms, $resourceId, $permissionId, $contextId) {
		$rules = [];
		foreach ([ '*',$resourceId] as $resId) {
			if (!isset($rolePerms[$resId])) {
				continue;
			}
			foreach (['*',$permissionId] as $permId) {

				if (!isset($rolePerms[$resId][$permId])) {
					continue;
				}

				if (!$contextId) {
					if (isset($rolePerms[$resId][$permId]['*'])) {
						$rules[] = ['rules' => $rolePerms[$resId][$permId]['*']['rules'], 'cid' => '*', 'rid' => $resId, 'pid' => $permId];
					}
					continue;
				}

				if (is_scalar($contextId)) {

					if (isset($rolePerms[$resId][$permId][$contextId])) {
						
						$rules[] = ['rules' => $rolePerms[$resId][$permId][$contextId]['rules'], 'cid' => $contextId, 'rid' => $resId, 'pid' => $permId];
						continue;
					}
					
					if (isset($rolePerms[$resId][$permId]['*'])) {
			 
						$rules[] = ['rules' => $rolePerms[$resId][$permId]['*']['rules'], 'cid' => '*', 'rid' => $resId, 'pid' => $permId];
					}
				}
				else {
 
					foreach ($rolePerms[$resId][$permId] as $cKey => $rule) {
						if ($contextId->match($cKey)) {
							$rules[] = $rule;
						}
					}
				}
			}
		}
		return $rules;
	}

}
