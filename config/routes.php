<?php

return FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'App\Controllers\MainController@index');
    $r->addRoute('POST', '/', 'App\Controllers\MainController@buildStatistic');
});