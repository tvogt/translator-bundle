# Translator Bundle

This Symfony 2 bundle provides a simple web-based interface for collaborative translation of i18n strings.



## Features
* translation strings are stored in the database
* console command to import existing YAML files included
* integrates with Symfony2 user management (ROLE_USER needed to access, ROLE_ADMIN required for administrative actions)
* user translations are stored as suggestions, can be voted on and need admin approval before they are loaded as a translation
* no documentation except this readme
* composer.json untested, use at your own risk (and let me know how spectacularily it blew up)


## Installation

The code is currently integrated with my [Might & Fealty ](http://mightandfealty.com) codebase in some places.
Here's what you will need to modify in order to install it into your Symfony 2 project:

You will definitely want to modify layout.html.twig extensively

The entities in Suggestion.orm.xml, Translation.orm.xml and Vote.orm.xml link to a user entity, you need to replace its path with one appropriate to your application

You need to add something like this to your security.yml (or .xml or whatever):

	access_control:
		- { path: ^/translator/admin, roles: ROLE_ADMIN }
		- { path: ^/translator, roles: ROLE_USER }

And finally, add something like this to your routing.yml (.xml, etc.):

	calitarus_translator:
   	resource: "@CalitarusTranslatorBundle/Resources/config/routing.yml"
    	prefix:   /translator



## Wishlist
Let me know if you fork and implement any of these:
* i18n capabilities of the Translator itself, right now all strings are hardcoded (not difficult, I just needed it quickly)
* database loader for translation strings, using the LoaderInterface.



## Changes
* initial commit
* added remarks and voting