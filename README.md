Demofony2
=========

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1ea90778-1408-4747-9e8d-d161205ddadf/big.png)](https://insight.sensiolabs.com/projects/1ea90778-1408-4747-9e8d-d161205ddadf)
[![Build Status](https://travis-ci.org/teclliure/demofony2.svg?branch=devel)](https://travis-ci.org/teclliure/demofony2)

Welcome to Demofony2 web application based on Symfony2 Full Stack Framework.

License
-------

This bundle is under the MIT license. See the complete license in file [LICENSE] (https://github.com/teclliure/demofony2/blob/master/LICENSE)

Application designed by [Teclliure][1]. Developed by [Flux][2] & [Marc Morales][3]. Code property of [Ajuntament de Premià de Mar][4]

[1]: http://www.teclliure.net/
[2]: http://www.flux.cat
[3]: mailto:marcmorales83@gmail.com
[4]: http://www.premiademar.cat/


Installation
------------

* Clone repository

* Execute "composer install"

* Set app/config/parameters.yml according to your needs

* Apply full permissions to web/media & web/uploads directories

* Config Apache Vhost server

* Config MySQL connection

Be sure to get Git & Composer installed before.


EER Diagram
-----------

You can found a database schema diagram [here] (https://github.com/teclliure/demofony2/blob/master/doc/EERdiagram.pdf)


Database reminders
------------------

Remeber to deal with Doctine commands to manage this scenarios:

* execute Doctrine database create command

* execute Doctrine migrations migrate command

* execute Doctrine fixtures load command (very important in test environment!)


Testing
-------

Before to run test, be sure to initialize test database & load fixtures.


Google Analytics API
--------------------

To configure Google Analytics API follow [this] (https://github.com/widop/google-analytics/blob/master/doc/usage.md) instructions:

Configure this params in parameters.yml

*    ga_api_client_id: ....q7thkh1f26i6147ovsumhfkgfph@developer.gserviceaccount.com

*    ga_api_profile_id: 'ga:12345678'

*    ga_api_private_key_file: '%kernel.root_dir%/Resources/bin/d47b32b48c945c6dcac5ed3c05e68b4637aad140-privatekey.p12'

*    ga_api_profile_id: 'ga:12345678'
    
To get the ga_profile_id enter in google analytics report of the account are configuring and get the last number in url after p key.

Example: [here] (https://www.google.com/analytics/web/?hl=ca&pli=1#report/visitors-overview/a41127899w70498117p12345678/)


Other APIs
----------

Remeber to generate new API keys according to your social networks.

* Facebook

* Twitter

* Mandrill


Frontend issues
---------------

All the assets has been managed with [Gulp] (http://gulpjs.com/) so you must install Node, Bower & Gulp before. Read [gulpfile.js] (https://github.com/teclliure/demofony2/blob/master/gulpfile.js) tasks to a better knowledgement.


Console command for update states
----------------------------------

This application have different console commands to update the state from Process participation, Proposals and Citizen forums.

These command can be executed by cron job once a day at 00:00h for example.

*   php app/console demofony2:process-participation:update-state --force

*   php app/console demofony2:proposal:update-state --force

*   php app/console demofony2:citizen-forum:update-state --force