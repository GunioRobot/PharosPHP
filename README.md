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
- Extensible module system, allowing developers to interact with PharosPHP core (Hooks API) and even interact with other modules

Requirements
------------

PharosPHP requires

- PHP 5.2+ (PHP 5.3+ for Active Record support)
- MySQL 4.1+, MySQL 5
