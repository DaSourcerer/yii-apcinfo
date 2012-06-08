apcinfo
=======
A module for monitoring and managing APC from within a Yii application


This modules is intended to provide roughly the same functionality as the apc.php that shipps with the documentation of the Alternative PHP Cache. The goal is to take a peek at the inner workings of APC and monitor things like cache usage, memory fragmentation etc.

This extension comes as a module as this is the easiest way to provide all the funcionality in one drop. However, it should be possible to rename and/or even relocate the main controller.

Please take note that this modules does not check for authentication or authorization in any way since I cannot make any assumptions of the auth mechanisms in other's applications. Please see to it that you grant permissions on this module only to the appropriate entities.


Contributing
------------

This module is still a work-in-progress. So any kind of feedback is very welcome.


Requirements
------------

* Yii v1.1.x
* APC
* Possibly the liquid plugin for the Blueprint CSS framework


Usage
-----

This extension can be used as a drop-in module. Just follow these steps:

Unzip the content of the ZIP to your application.modules directory
Find the modules-stanza (create it if it doesn't exist yet) in your config and add the apcinfo module like this:

    'modules' => array(
        //...,
        'apcinfo',
    ),


Enjoy! :)
