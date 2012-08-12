What is this?
=============
A hobby project of mine. Also presented at [Infoeducatie in 2010][http://infoeducatie.ro/2010/rezultate.php?page=web].

The project was not continued or maintained after the contest, so this are the files from 2010 with small modifications. Of course  *config.php* and all the content and images are not in this repository.

Setup
-----

I'm sorry but you will need to change every occurrence of *.weskate.ro* to whatever host you are using to play with this code. Check in every file: PHP, JS, CSS, whatever...

Dump *structure.sql* into a MySQL database and setup *config.php* accordingly.

inside config.php
-----------------

There are some variables (they'll be **unset()** after they're not required anymore):

    $db_host
    $db_user
    $db_name
    $db_pass
    $recaptcha_public
    $recaptcha_private

It is straightforward what information you need to put there. :)

Is this good?
=============

This is a good example of bad code structure. *DO NOT WRITE LIKE THIS.* I just share this code for... fun, I think.

The Romania map used in *prin-tara* section might be useful for someone - it was a long process to get that result...

It is live!
===========

You can see this code live at http://weskate.ro.

Copyrights and stuff
====================

GPL v3
------
This piece of software is released under GNU/GPL v3 licence. See COPYING for the full licence text.

You can use this code as long as you keep it open-source.

Third parties
-------------

Oh, yeah. There are a lot of things used from the internet. Starting from [jscolor][http://jscolor.com], mapper.js, some bits of code from [PHP-Fusion v7][http://php-fusion.co.uk] and probably many more - I can't remember. Most of them have their copyright notice with them - respect their licences.
