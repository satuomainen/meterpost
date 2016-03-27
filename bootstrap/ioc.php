<?php

/*
|--------------------------------------------------------------------------
| Wire the IoC dependencies
|--------------------------------------------------------------------------
|
| see https://laravel.com/docs/4.2/ioc
|
*/

App::singleton(DataseriesService::SERVICE_NAME, function () {
    return new DataseriesService();
});

App::singleton(ApiKeyService::SERVICE_NAME, function () {
    return new ApiKeyService();
});
