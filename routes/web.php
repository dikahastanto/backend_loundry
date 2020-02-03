<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');
$router->post('/search', 'AuthController@search');
$router->get('/getprofile/{id}', 'AuthController@getData');

// produk route
$router->post('/produk/insert', 'ProdukController@insert');
$router->get('/produk/getuserporduk/{id}', 'ProdukController@getByIdUser');
$router->get('/produk/allporduk', 'ProdukController@getAll');

// transaksi route
$router->post('/transaksi/insert', 'TransaksiController@insert');
$router->get('/transaksi/pelanggan/{id}', 'TransaksiController@getPelanggan');
$router->get('/transaksi/owner/{id}', 'TransaksiController@getTransaksiByOwner');
$router->post('/transaksi/confirm/{id}', 'TransaksiController@confirm');
$router->post('/transaksi/selesai/{id}', 'TransaksiController@selesai');