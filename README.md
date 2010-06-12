Project Overview
================

PharosPHP is a lightweight Object-Oriented framework aimed at providing common and useful functionality to developers, in order to create powerful and flexible applications quickly.

Features
--------

- MVC Architecture
- Robust template system
	- Layouts (application level, controller level)
	- Views (PHP/HTML mixup or use bundled Profile module to quickly build forms)
	- Caching (caching is as simple as $this->output->cache(5) for full HTML content caching of 5 minutes)
- Active Record support (required for the use of Models, requires PHP 5.3+)
- Powerful Router system
- Clean and extensible settings API, using both YAML and MySQL for persistent storage
- Several bundled modules, to help developers begin utilizing familiar Open Source projects
	- SWFUpload
	- jQuery
	- PHP Quick Profiler
	- RMail
	- YAML
	- MSFT Excel Parser
	- Apple Plist (plain text and binary) read/write
	- TinyMCE
	- Several more...
- Extensible module API, allowing developers to interact with PharosPHP core (Hooks API) and even interact with other modules

Requirements
------------

PharosPHP requires

- PHP 5.2+ (PHP 5.3+ for Active Record support)
- MySQL 4.1+, MySQL 5


Routing
-------

PharosPHP comes with a custom Router class that makes developing powerful web applications with clean SEO URLs a snap.

### Auto Generated Routing

By default, the Router class maps URLs to a controller, action, and associated parameters.  

For example, the URL 

> /posts/mark-as-favorite/param1/param2/param3/

would be map to 

> /application/controllers/PostsController.php

and all the following method:

	class PostsController extends Controller {
		
		public function markAsFavorite($param1, $param2, $param3) {
			// do something exciting
			// $param1, $param2, $param3 are all strings
		}
		
	}

### Application Defined Routing

Sometimes it is necessary to have more fine-grained control over the routing in your application.  To do so, you define custom routes.  The following examples would be placed in the *application.yml* configuration file under *routes.connections* to enable custom routing.

For example, to connect a URL of "post-5/true/2008-04-13/", include the following route:

	pattern: 'post-:id/:repost/:month/?'
	controller: PostsController
	action: edit
	params: 
		:repost => (true|false)
		:month => ([[:digit:]]{4}-[[:digit:]]{2}-[[:digit:]]{2})
		
This application defined route would map to:

> /application/controller/PostsController.php

	class PostsController extends Controller {
		
		public function edit($params) {
			
			// do something exciting
			// $params is associative array with keys defined in our route (params)
			// $params also includes the 3 application defined paramaters of :controller, :action, and :id (if found in the pattern)
			
			print_r($params);			
			
		}
		
	}
	
The output would be:
	array {
		:id => 5,
		:repost => true,
		:month => 2008-04-13
	}
	
## Model-View-Controller

According to Wikipedia, MVC is defined as:
> Model–View–Controller (MVC) is a software architecture, currently considered an architectural pattern used in software engineering. The pattern isolates "domain logic" (the application logic for the user) from input and presentation (GUI), permitting independent development, testing and maintenance of each.

PharosPHP does it's best to utilize the MVC design pattern by enforcing a stricter separation between Controller and View.  For example:

	class BooksController extends Controller {
		
		public function someMethod($params) {
			
			// Setup some variables that are really important (controller calculations)
			$books = Books::all();
			$date = new DateTime();
			$salt = md5(rand(1,1000));
			
			// To expose these to the view, we must explicitly assign
			$this->output->set("books", $books);
			$this->output->set("date", $date);
			
			// Now render a static string, followed by a view in the views directory
			$this->output->view("<h1>My View</h1>");
			$this->output->view("my-view.php");
			
		}
		
	}
	
The contents of "my-view.php" contain:

	<p>Glad you could join me on <?=$date->format("F jS, Y")?>!</p>
	<p>The salt is <?=$salt?></p>

	<? if ( !empty($books): foreach($books as $book): ?>
	<span class="book-title"><?=$book->title?></span><br />
	<? endforeach; else: ?>
	<h3>Looks like there aren't any books!</h3>
	<? endif; ?>
	
There would be an error when trying to use the contents of "$salt" as the variable does not exist in the view's scope
	