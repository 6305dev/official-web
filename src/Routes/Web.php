<?php

use Bramus\Router\Router;

$router = new Router();
$router->setNamespace('\App\Controllers');
$router->get('/', 'ViewController@home');
$router->get('/profil/([a-z0-9_-]+)', 'ViewController@preview');
$router->get('/pelayanan/([a-z0-9_-]+)', 'ViewController@preview');
$router->get('/publikasi/([a-z0-9_-]+)', 'ViewController@preview');
$router->get('/dokumen/([a-z_-]+)', 'ViewController@document');

$router->before('POST', '/upload', 'AuthController@limitAccess');
$router->post('/upload', 'UploadController@uploadFile');

$router->before('POST', '/create-article', 'AuthController@limitAccess');
$router->post('/create-article', 'AdminController@createArticle');

$router->before('POST', '/update-article', 'AuthController@limitAccess');
$router->post('/update-article', 'AdminController@updateArticle');

$router->before('GET', '/article/(\d+)', 'AuthController@limitAccess');
$router->get('/article/(\d+)', 'AdminController@getArticle');

$router->before('DELETE', '/delete-article/(\d+)', 'AuthController@limitAccess');
$router->delete('/delete-article/(\d+)', 'AdminController@deleteArticle');

$router->before('POST', '/create-document', 'AuthController@limitAccess');
$router->post('/create-document', 'AdminController@createDocument');

$router->before('DELETE', '/delete-document/(\d+)', 'AuthController@limitAccess');
$router->delete('/delete-document/(\d+)', 'AdminController@deleteDocument');

$router->before('GET', '/sliders', 'AuthController@limitAccess');
$router->get('/sliders', 'AdminController@getSliders');

$router->before('POST', '/update-slider', 'AuthController@limitAccess');
$router->post('/update-slider', 'AdminController@updateSlider');

$router->before('GET', '/login', 'AuthController@logged');
$router->get('/login', 'ViewController@login');

$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->before('GET', '/dashboard', 'AuthController@authenticate');
$router->get('/dashboard', 'ViewController@dashboard');

$router->set404('ViewController@notFound');
$router->run();
