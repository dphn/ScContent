ScContent
=======================

This module in process.
At this time, the module works only with the database MySql.

The module allows you to edit the content, manage themes and widgets.
Nevertheless, the basic widgets kit is provided as a separate module (https://github.com/dphn/ScWidgets).
You can easily create your own  widgets kit for your specific application.

Introduction
-----------------
It is a simple module for editing the content based on Zend Framework 2.

![Screenshot] (/docs/img/sc-content.png?raw=true)

Requirements
-----------------
* php >= 5.4.0
* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)
* [BjyAuthorize](https://github.com/bjyoungblood/BjyAuthorize) (latest master)
* [ZfcUser](https://github.com/ZF-Commons/ZfcUser) (latest master)
* [ZfcBase](https://github.com/ZF-Commons/ZfcBase) (latest master)


Module update
--------------

Attention! The module is still in the development stage.
The versioning of database migration is not supported yet.
***Please, after any update, delete any table from module database!

Sorry for the inconvenience, improvements are planned in the near time.***

Installation
--------------
### Note

The best way to get acquainted with the functionality of widgets - [ScWidgets](https://github.com/dphn/ScWidgets) module installed immediately.

### Main Setup

#### By cloning project

1. Install the [BjyAuthorize](https://github.com/bjyoungblood/BjyAuthorize) ZF2 module
   by cloning it into `./vendor/`.
2. Install the [ZfcBase](https://github.com/ZF-Commons/ZfcBase) ZF2 module
   by cloning it into `./vendor/`.
3. Install the [ZfcUser](https://github.com/ZF-Commons/ZfcUser) ZF2 module
   by cloning it into `./vendor/`.
4. Clone this project into your `./vendor/` directory.

#### With composer

1. Add this project in your composer.json:

    ```json
    "require": {
        "dphn/sc-content" : "dev-master"
    }
    ```
 
2. Now tell composer to download ScContent by running the command:

    ```bash
    $ php composer.phar update
    ```
    
#### Post installation

1. Enabling it in your `application.config.php`file.

    ```php
    <?php
    return array(
        'modules' => array(
            // ...
            'ZfcBase',
            'ZfcUser',
            'BjyAuthorize',
            'ScContent',
        ),
        // ...
    );
    ```
    
2. Further installation is automatic.


