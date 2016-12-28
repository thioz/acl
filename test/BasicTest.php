<?php
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
	protected $acl;

	/**
	 * @return \Gracious\Acl\AclManager
	 */
	protected function getAcl()
	{
 		return $this->acl;
	}

	public function setUp()
	{
		$this->acl = new \Gracious\Acl\AclManager();
	}

	public function testHasResources()
	{
		$acl = $this->getAcl();
		$acl->addResource(new \Gracious\Acl\AclResource('post'));

		$this->assertTrue($acl->hasResource('post'));
		$this->assertFalse($acl->hasResource('page'));
	}

	public function testCatchAllPermission()
	{
		$acl = $this->getAcl();
		$acl->addResource('post');

		$acl->addRole('admin', 'Admin');
		$acl->addRole('public', 'Pubic');

		$acl->allow('admin');

		$user = new MockUser(1, 'admin');

		$this->assertTrue($acl->isAllowed($user, 'post'));
		$this->assertTrue($acl->isAllowed($user, 'notexistingresource'));
	}

	public function testNotDefinedPermission()
	{
		$acl = $this->getAcl();
		$acl->addResource(new \Gracious\Acl\AclResource('post'));

		$acl->addRole('admin', 'Admin');
		$acl->addRole('public', 'Pubic');

		$acl->allow('admin');

		$user = new MockUser(1, 'admin');
		$public = new MockUser(2, 'public');

		$acl->allow('admin');

		$this->assertFalse($acl->isAllowed($public, 'post'));
	}

	public function testDenyResourceContext()
	{
		$acl = $this->getAcl();
		$acl->addResource(new \Gracious\Acl\AclResource('post'));

		$acl->addRole('admin', 'Admin');
		$acl->addRole('public', 'Pubic');

		$acl->allow('admin');

		$user = new MockUser(1, 'admin');
		$public = new MockUser(2, 'public');

		$acl->allow('admin', 'post', 'delete');

		$acl->deny('admin', 'post', 'delete', 1);

		$this->assertFalse($acl->isAllowed($user, 'post', 'delete', 1));
	}


}
