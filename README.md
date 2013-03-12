# Translator Bundle

This Symfony 2 bundle provides a simple web-based interface for collaborative translation of i18n strings.

[![Build Status](https://travis-ci.org/tvogt/translator-bundle.png?branch=master)](https://travis-ci.org/tvogt/translator-bundle)


## Features
* translation strings are stored in the database
* console command to import existing YAML files included
* integrates with Symfony2 user management (ROLE_USER needed to access, ROLE_ADMIN required for administrative actions)
* user translations are stored as suggestions, can be voted on and need admin approval before they are loaded as a translation
* no documentation except this readme
* composer.json untested, use at your own risk (and let me know how spectacularily it blew up)


## Installation
The code is currently integrated with my [Might & Fealty](http://mightandfealty.com) codebase in some places.
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


## Inline Translation
There is an experimental Twig extension included. To enable it, add this to your config.yml or services.yml:

    services:
      twig.extension.inlinetrans:
        class:      Calitarus\TranslatorBundle\Twig\InlineTranslationExtension
        arguments:  [@translator]
        tags:
            - { name: twig.extension }

Then you have four new filters available, i_trans and i_transchoice as replacements for trans and transchoice as well
as it_trans and it_transchoice as replacements + |title functionality (because chaining the output of i_trans with title
breaks the HTML tags).

This wraps all translated strings in span tags that include the domain and key values. Right now that is all that it does.
From there on, however, it should be easy to do a bit of jQuery and AJAX to make all translated strings on the current
page editable, allowing translators to work right in the application, where they have the full context available.

This is pretty much an incomplete/wishlist feature. I am not happy with the way it requires seperate Twig tags and would much
rather prefer I could overload trans and transchoice and solve the HTML breakage issues. I would he happy if anyone can
figure that out and contribute the fix back.



## Wishlist
Let me know if you fork and implement any of these:
* database loader for translation strings, using the LoaderInterface.

