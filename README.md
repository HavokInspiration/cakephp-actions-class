# CakePHP ActionsClass plugin

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://travis-ci.org/HavokInspiration/cakephp-actions-class.svg?branch=master)](https://travis-ci.org/HavokInspiration/wrench)
[![codecov.io](https://codecov.io/github/HavokInspiration/cakephp-actions-class/coverage.svg?branch=master)](https://codecov.io/github/HavokInspiration/cakephp-actions-class?branch=master)

**This is a plugin in development. Use it at your own risk.** 

This plugin gives you the ability to manage your CakePHP Controller actions as single classes. Each action of your Controllers will be managed in its own object.

## Requirements

- PHP >= 7.0.0
- CakePHP 3.4.X *

_* It might work on lesser versions, but I did not test it (and do not plan to)._

## Installation

You can install this plugin into your CakePHP application using [Composer](https://getcomposer.org).

The recommended way to install it is:

```
composer require havokinspiration/cakephp-actions-class
```

## Loading the plugin

You can load the plugin using the shell command:

```
bin/cake plugin load HavokInspiration/ActionsClass --bootstrap
```

Or you can manually add the loading statement in the **bootstrap.php** file of your application:

```php
Plugin::load('HavokInspiration/ActionsClass', ['bootstrap' => true]);
```

**Loading the plugin bootstrap file is mandatory.**

## Usage

By default, CakePHP controller management is based around one controller (which represents one part of your application, for instance "Posts") which is a single class containing one public method per actions needed to be exposed in your application:

```php
// in src/Controller/PostsController.php
namespace App\Controller;

use Cake\Controller;

class PostsController extends Controller
{

    public function index() {}
    public function add() {}
    public function edit($id) {}
}
```

As your application grows, your controllers grow as well. In the end, on large applications, you can end up with big controller files with lots of content, making it hard to read through. You might even be in the case where you need to have methods specific to some actions in the middle of other methods dedicated to other actions.

This is where the **cakephp-actions-class** plugin is useful. When enabled, **you can have your controllers actions as single classes**.

So the `PostsController` example given above would become:
 
```php
// in src/Controller/Posts/IndexAction.php
namespace App\Controller\Posts;

use HavokInspiration\ActionsClass\Controller\Action;

class IndexAction extends Action 
{
    public function execute() {}
}
```

```php
// in src/Controller/Posts/AddAction.php
namespace App\Controller\Posts;

use HavokInspiration\ActionsClass\Controller\Action;

class AddAction extends Action 
{
    public function execute() {}
}
```

```php
// in src/Controller/Posts/EditAction.php
namespace App\Controller\Posts;

use HavokInspiration\ActionsClass\Controller\Action;

class EditAction extends Action 
{
    public function execute($id) {}
}
```

Living in the following directory structure :

```
src/
  Controller/
    Posts/
      AddAction.php
      EditAction.php
      IndexAction.php
```

Your `Action` classes are only expected to hold an `execute()` method. It can receive passed parameters as in regular controller actions (meaning the URL `/posts/edit/5` will pass `5` to the argument `$id` in the `execute()` method of the `EditAction` class in our previous example).

## Compatibility

This plugin was designed to have a maximum compatibility with the regular CakePHP behavior.

### Fallback to CakePHP regular behavior

If you wish to use this plugin in an existing application, it will first try to provide a response using an `Action` class. If an action class matching the routing parameters can not be found, it will let CakePHP fallback to its regular behavior (meaning looking for a `Controller` class).

This also means that you can develop a plugin with controllers implementing this behavior without breaking the base application (since, when the **cakephp-actions-class** plugin is loaded, the Dispatcher will first try to load an `Action` class and fallback to the regular `Controller` dispatching behavior if it can not find a proper `Action` class to load).

### Everything you do in Controller can be done in an Action class

Under the hood, `Action` classes are instances of `\Cake\Controller\Controller`, meaning that **everything you do in a regular `Controller` class can be done in an `Action` class**. 
 
#### Loading Components

```php
// in src/Controller/Posts/EditAction.php
namespace App\Controller\Posts;

use HavokInspiration\ActionsClass\Controller\Action;

class EditAction extends Action 
{
    public function initialize() 
    {
        $this->loadComponent('Flash');
    }
    
    public function execute($id)
    {
        // some logic
        $this->Flash->success('Post updated !');
    }
}
```

#### Actions in Controllers under a routing prefix

`Action` classes can live under a routing prefix or a plugin :

```php
// in src/Controller/Posts/EditAction.php

// Assuming that `Admin` is a routing prefix
namespace App\Controller\Admin\Posts;

use HavokInspiration\ActionsClass\Controller\Action;

class EditAction extends Action 
{    
    public function execute($id)
    {
    }
}
```

### No-op methods

As seen above, `Action` classes are instance of `\Cake\Controller\Controller`. Some methods in this class are related to actions. But since we are now having objects that represent actions, two methods had to be made "no-op" : `\Cake\Controller\Controller::isAction()` and `\Cake\Controller\Controller::setAction()`. Using these methods in an `Action` subclass will have no effect.  

## Configuration

### Strict Mode

As seen above, the plugin will let CakePHP handle the request in its regular dispatching cycle if an action can not be found. However, if you wish to only use `Action` classes, you can enable the strict mode. With strict mode enabled, the plugin will throw an exception if it can not find an `Action` class matching the request currently being resolved.
 
To enable the strict mode, set it using the `Configure` object in the **bootstrap.php** file of your application:

```php
Configure::write('ActionsClass.strictMode', true);
```

## Roadmap

- [x] Serve a simple action  
- [x] Serve an action behind a routing prefix  
- [x] Let the system fallback to CakePHP dispatching cycle in case an Action does not exist  
- [x] Serve an action behind a nested routing prefix  
- [x] Serve an action from a plugin
- [x] Serve an action from a plugin, behind a (nested) routing prefix    
- [x] Test working with Components
    - [x] Loading a component in an action
    - [x] Loading two different components in two different action
- [x] Try working with an "AppAction"
- [ ] Check that integration tests works with this plugin  
- [x] Start writing unit tests  
- [x] Write some docs  
- [x] Run a sniffer  

## Contributing

If you find a bug or would like to ask for a feature, please use the [GitHub issue tracker](https://github.com/HavokInspiration/cakephp-actions-class/issues).
If you would like to submit a fix or a feature, please fork the repository and [submit a pull request](https://github.com/HavokInspiration/cakephp-actions-class/pulls).

### Coding standards

This repository follows the PSR-2 standard. Some methods might be prefixed with an underscore because they are an overload from existing methods inside the CakePHP framework.  
Coding standards are checked when a pull request is made using the [Stickler CI](https://stickler-ci.com/) bot. 

## License

Copyright (c) 2015 - 2017, Yves Piquel and licensed under [The MIT License](http://opensource.org/licenses/mit-license.php).
Please refer to the LICENSE.txt file.
