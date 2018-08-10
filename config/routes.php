<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::prefix('management', function (RouteBuilder $routes) {
    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->connect('/account', ['controller' => 'Account', 'action' => 'index']);
    $routes->connect('/account/ajax', ['controller' => 'Account', 'action' => 'ajax']);
    $routes->connect('/account/edit/*', ['controller' => 'Account', 'action' => 'edit']);
    $routes->connect('/schedule/month/*', ['controller' => 'Schedule', 'action' => 'month']);
    $routes->connect('/schedule/week/*', ['controller' => 'Schedule', 'action' => 'week']);
    $routes->connect('/schedule/day/*', ['controller' => 'Schedule', 'action' => 'day']);
    $routes->connect('/importreserve/*', ['controller' => 'Reserve', 'action' => 'importReserve']);
    $routes->connect('/reserve/*', ['controller' => 'Reserve', 'action' => 'index']);
    $routes->connect('/counsel_note/info/*', ['controller' => 'CounselNote', 'action' => 'info']);
    $routes->connect('/counsel_note/note/*', ['controller' => 'CounselNote', 'action' => 'note']);
    $routes->connect('/shift/list/*', ['controller' => 'Shift', 'action' => 'index']);
    $routes->connect('/concierge/list', ['controller' => 'Concierge', 'action' => 'index']);
    $routes->connect('/concierge/edit/*', ['controller' => 'Concierge', 'action' => 'edit']);
    $routes->connect('/concierge/ajax', ['controller' => 'Concierge', 'action' => 'ajax']);
    $routes->connect('/nursery/reserve/*', ['controller' => 'Nursery', 'action' => 'reserve']);
    $routes->connect('/nursery/schedule/*', ['controller' => 'Nursery', 'action' => 'schedule']);
    $routes->connect('/nursery/setting/*', ['controller' => 'Nursery', 'action' => 'setting']);

    $routes->connect('/keep-session', ['controller' => 'Management', 'action' => 'ajax']);
    /* $routes->fallbacks(DashedRoute::class); */
});

Router::prefix('concierge', function (RouteBuilder $routes) {

    $routes->connect('/top', ['controller' => 'Index', 'action' => 'index']);
    //$routes->connect('/profile', ['controller' => 'Profile', 'action' => 'index']);
    $routes->connect('/schedule/month/*', ['controller' => 'Schedule', 'action' => 'month']);
    $routes->connect('/schedule/week/*', ['controller' => 'Schedule', 'action' => 'week']);
    $routes->connect('/schedule/day/*', ['controller' => 'Schedule', 'action' => 'day']);

});

Router::prefix('mypage', function (RouteBuilder $routes) {


    $routes->connect('/reserve/*', ['controller' => 'Reserve', 'action' => 'form']);
    $routes->connect('/reserve/list', ['controller' => 'Reserve', 'action' => 'index']);
    $routes->connect('/nursery', ['controller' => 'Nursery', 'action' => 'form']);
    $routes->connect('/nursery/ajaxshorttime', ['controller' => 'Nursery', 'action' => 'ajaxshorttime']);
    $routes->connect('/nursery/list', ['controller' => 'Nursery', 'action' => 'index']);

    $routes->connect('/keep-session', ['controller' => 'Mypage', 'action' => 'ajax']);
});

/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
