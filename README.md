# Gracious Acl

A simple but powerfull ACL implementation



# Quickstart

```php
// create a new instance of the manager
$acl = new Gracious\Acl\AclManager();
```
### Defining resources
```php
// register a basic resource 
$acl->addResource(new \Gracious\Acl\Resource('page'));

// register an eloquent model resource
$acl->addResource(new \Gracious\Acl\Eloquent\ModelResource('post', MockPost::class));
```
### Defining roles
```php
$acl->addRole('admin','Admin');
$acl->addRole('editor','Editor');
```
### Defining simple permissions
```php
// allow all the things for the admin role
$acl->allow('admin');

$acl->allow('editor','page','edit');
$acl->allow('editor','page','view');

```
### Defining fine grained permissions
```php
// allow editor to delete page with ID 10
$acl->allow('editor','page','delete',10);

// allow editor to delete any post, which belongs to himself 
$acl->allow('editor','post','delete','*', ModelOwnerFilter::class);

```

