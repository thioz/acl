<?php

require_once '../vendor/autoload.php';
require_once './src/mock.php';

$adminUser = new MockUser(1,'admin');
$editUser = new MockUser(2,'editor');

$acl = new Gracious\Acl\AclManager();

$acl->addResource(new \Gracious\Acl\Eloquent\ModelResource('post', MockPost::class));
$acl->addResource(new \Gracious\Acl\Resource('page'));

$acl->addRole('admin','Admin');
$acl->addRole('editor','Editor');

$acl->allow('admin');

$acl->allow('editor','page','edit');
$acl->allow('editor','page','view');
$acl->allow('editor','page','delete',10);
$acl->allow('editor','post','delete','*', ModelOwnerFilter::class);


print_r('Can editor delete page'.PHP_EOL);
var_dump($acl->isAllowed($editUser,'page','delete'));

print_r('Can editor delete page 10'.PHP_EOL);
var_dump($acl->isAllowed($editUser,'page','delete',10));

print_r('Can editor delete post 1'.PHP_EOL);
var_dump($acl->isAllowed($editUser,'post','delete',1));

print_r('Can editor delete post 2'.PHP_EOL);
var_dump($acl->isAllowed($editUser,'post','delete',2));

print_r('Can editor delete post 3'.PHP_EOL);
var_dump($acl->isAllowed($editUser,'post','delete',3));

print_r('Can admin delete post 3'.PHP_EOL);
var_dump($acl->isAllowed($adminUser,'post','delete',3));

