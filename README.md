# Project Overview

PharosPHP is a lightweight Object-Oriented framework aimed at providing common and useful functionality to developers, in order to create powerful and flexible applications quickly.

## Features

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

## Requirements

PharosPHP requires

- PHP 5.2+ (PHP 5.3+ for Active Record support)
- MySQL 4.1+, MySQL 5

## Active Record

PharosPHP brings Ruby on Rails style Active Record to PHP, complete with dynamic methods (very powerful)!
>Active record is an approach to access data in a database. A database table or view is wrapped into a class, thus an object instance is tied to a single row in the table. After creation of an object, a new row is added to the table upon save. Any object loaded gets its information from the database; when an object is updated, the corresponding row in the table is also updated. The wrapper class implements accessor methods or properties for each column in the table or view.

> http://github.com/kla/php-activerecord
> http://www.phpactiverecord.org/

>
	$post = Post::find(1);
	echo $post->title; # 'My first blog post!!'
	echo $post->author_id; # 5

>	// also the same since it is the first record in the db
	$post = Post::first();

>	// finding using dynamic finders
	$post = Post::find_by_name('The Decider');
	$post = Post::find_by_name_and_id('The Bridge Builder',100);
	$post = Post::find_by_name_or_id('The Bridge Builder',100);

>	// finding using a conditions array
	$posts = Post::find('all',array('conditions' => array('name=? or id > ?','The Bridge Builder',100)));


## Routing

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
	
The special reserved params of :controller, :action, and :id have predefined meaning (read: regular expressions	) and are used by the system.  
> Both the URL: "/blog/posts/view/13/" and URL "/blog/comments/view/13/" would match by the basic route:  "/blog/:controller/:action/:id/"

	class PostsController extends Controller {
		public function view($params) {
			$this->set("post", Post::find_by_id($params[":id"]));
			$this->output->view("post-view.php");
		}
	}
	
	class CommentsController extends Controller {
		public function view($params) {
			$this->set("comments", Comment::find_all_by_post_id($params[":id"]));
			$this->output->view("comments-view.php");
		}
	}
	
	
=======
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
	