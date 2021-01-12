<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
/*RUTAS POST*/

Route::POST('services/test','StoresController@test');
Route::GET('services/test','StoresController@test');

Route::POST('services/products','ProductsController@products');
Route::GET('services/products','ProductsController@products');

Route::POST('services/productSuggestion','ProductsController@productSuggestion');
Route::GET('services/productSuggestion','ProductsController@productSuggestion');


Route::POST('services/getProductById','ProductsController@getProductById');
Route::GET('services/getProductById','ProductsController@getProductById');

Route::POST('services/getProductsByString','ProductsController@getProductsByString');
Route::GET('services/getProductsByString','ProductsController@getProductsByString');

Route::POST('services/getProductsByCategory','ProductsController@getProductsByCategory');
Route::GET('services/getProductsByCategory','ProductsController@getProductsByCategory');

Route::POST('services/getProductPrice','ProductsController@getProductPrice');
Route::GET('services/getProductPrice','ProductsController@getProductPrice');

Route::POST('services/getPriceByCategoryAndSize','ProductsController@getPriceByCategoryAndSize');
Route::GET('services/getPriceByCategoryAndSize','ProductsController@getPriceByCategoryAndSize');

Route::POST('services/getProducSizes','ProductsController@getProducSizes');
Route::GET('services/getProducSizes','ProductsController@getProducSizes');

Route::POST('services/getPizza2IngPrice','ProductsController@getPizza2IngPrice');
Route::GET('services/getPizza2IngPrice','ProductsController@getPizza2IngPrice');

Route::POST('services/getPizzaEspPrice','ProductsController@getPizzaEspPrice');
Route::GET('services/getPizzaEspPrice','ProductsController@getPizzaEspPrice');

Route::POST('services/categories','CategoriesController@categories');
Route::GET('services/categories','CategoriesController@categories');

Route::POST('services/getCategoryById','CategoriesController@getCategoryById');
Route::GET('services/getCategoryById','CategoriesController@getCategoryById');

Route::POST('services/subcategories','CategoriesController@subcategories');
Route::GET('services/subcategories','CategoriesController@subcategories');

Route::POST('services/getSubcategoryById','CategoriesController@getSubcategoryById');
Route::GET('services/getSubcategoryById','CategoriesController@getSubcategoryById');

/*stores*/
Route::POST('services/stores','StoresController@stores');
Route::GET('services/stores','StoresController@stores');

Route::POST('services/getStoreById','StoresController@getStoreById');
Route::GET('services/getStoreById','StoresController@getStoreById');
/*ZONES*/
Route::POST('services/zones','ZonesController@zones');
Route::GET('services/zones','ZonesController@zones');

/*CLIENT*/
Route::POST('services/getClientById','ClientsController@getClientById');
Route::GET('services/getClientById','ClientsController@getClientById');

Route::POST('services/getClientByEmail','ClientsController@getClientByEmail');
Route::GET('services/getClientByEmail','ClientsController@getClientByEmail');

Route::POST('services/editClientById','ClientsController@editClientById');
Route::GET('services/editClientById','ClientsController@editClientById');

Route::POST('services/register','ClientsController@register');
Route::GET('services/register','ClientsController@register');

/*addresses*/
Route::POST('services/adresses','LocationsController@adresses');
Route::GET('services/adresses','LocationsController@adresses');

Route::POST('services/getWarrantyUbeById','LocationsController@getWarrantyUbeById');
Route::GET('services/getWarrantyUbeById','LocationsController@getWarrantyUbeById');

Route::POST('services/cities','LocationsController@cities');
Route::GET('services/cities','LocationsController@cities');

Route::POST('services/getCityById','LocationsController@getCityById');
Route::GET('services/getCityById','LocationsController@getCityById');

Route::POST('services/getRepublicStateById','LocationsController@getRepublicStateById');
Route::GET('services/getRepublicStateById','LocationsController@getRepublicStateById');

Route::POST('services/getCitiesByRepublicStateId','LocationsController@getCitiesByRepublicStateId');
Route::GET('services/getCitiesByRepublicStateId','LocationsController@getCitiesByRepublicStateId');

Route::POST('services/republicStates','LocationsController@republicStates');
Route::GET('services/republicStates','LocationsController@republicStates');

Route::POST('services/editAddressById','ClientsController@editAddressById');
Route::GET('services/editAddressById','ClientsController@editAddressById');

Route::POST('services/addAddressClient','ClientsController@addRelAddressClient');
Route::GET('services/addAddressClient','ClientsController@addRelAddressClient');

Route::POST('services/getAddressById','ClientsController@getAddressById');
Route::GET('services/getAddressById','ClientsController@getAddressById');

Route::POST('services/deleteAddressById','ClientsController@deleteAddressById');
Route::GET('services/deleteAddressById','ClientsController@deleteAddressById');

/*SIZES*/
Route::POST('services/sizes','SizesController@sizes');
Route::GET('services/sizes','SizesController@sizes');

Route::POST('services/getSizesByCategoryId','SizesController@getSizesByCategoryId');
Route::GET('services/getSizesByCategoryId','SizesController@getSizesByCategoryId');

Route::POST('services/getSizeById','SizesController@getSizeById');
Route::GET('services/getSizeById','SizesController@getSizeById');

/*INGREDIENTS*/

Route::POST('services/ingredients','IngredientsController@ingredients');
Route::GET('services/ingredients','IngredientsController@ingredients');

Route::POST('services/getIngredientById','IngredientsController@getIngredientById');
Route::GET('services/getIngredientById','IngredientsController@getIngredientById');

Route::POST('services/getIngredientByIdCategory','IngredientsController@getIngredientByIdCategory');
Route::GET('services/getIngredientByIdCategory','IngredientsController@getIngredientByIdCategory');

Route::POST('services/getCategoryIngredients','IngredientsController@getCategoryIngredients');
Route::GET('services/getCategoryIngredients','IngredientsController@getCategoryIngredients');

Route::POST('services/getCategoryIngredientById','IngredientsController@getCategoryIngredientById');
Route::GET('services/getCategoryIngredientById','IngredientsController@getCategoryIngredientById');

Route::POST('services/getExtrachessePrice','IngredientsController@getExtrachessePrice');
Route::GET('services/getExtrachessePrice','IngredientsController@getExtrachessePrice');

Route::POST('services/getChesseBorderPrice','IngredientsController@getChesseBorderPrice');
Route::GET('services/getChesseBorderPrice','IngredientsController@getChesseBorderPrice');

Route::POST('services/getPanPizzaPrice','IngredientsController@getPanPizzaPrice');
Route::GET('services/getPanPizzaPrice','IngredientsController@getPanPizzaPrice');

Route::POST('services/getIngredientsBySpecialty','IngredientsController@getIngredientsBySpecialty');
Route::GET('services/getIngredientsBySpecialty','IngredientsController@getIngredientsBySpecialty');

Route::POST('services/getIngredientByIdRate','IngredientsController@getIngredientByIdRate');
Route::GET('services/getIngredientByIdRate','IngredientsController@getIngredientByIdRate');


/*CHESE BORDER*/


Route::POST('services/getChesseBorderItems','IngredientsController@getChesseBorderItems');
Route::GET('services/getChesseBorderItems','IngredientsController@getChesseBorderItems');


/*PROMOTIONS*/
Route::POST('services/getPromotions','PromotionsController@getPromotions');
Route::GET('services/getPromotions','PromotionsController@getPromotions');

Route::POST('services/getPromotionById','PromotionsController@getPromotionById');
Route::GET('services/getPromotionById','PromotionsController@getPromotionById');

/*ORDERS*/
Route::POST('services/createOrder','OrdersController@createOrder');
Route::GET('services/createOrder','OrdersController@createOrder');

Route::POST('services/getOrderDetail','OrdersController@getOrderDetail');
Route::GET('services/getOrderDetail','OrdersController@getOrderDetail');

Route::POST('services/verifyDiscountCode','OrdersController@verifyDiscountCode');
Route::GET('services/verifyDiscountCode','OrdersController@verifyDiscountCode');

/*ORDERS PROCCESSOR*/
Route::GET('services/OrdersProcessor/{resource}','ProcessorController@OrdersProcessor');

/*DISCOUNTS PER PRODUCT*/
//getDSCstatus
Route::POST('services/getDSCstatus','StoresController@getDSCstatus');
Route::GET('services/getDSCstatus','StoresController@getDSCstatus');

//TRACKING*/getOnlineStatusOnStore
Route::POST('services/getOnlineInfoStore','TrackingOrderController@getOnlineInfoStore');
Route::GET('services/getOnlineInfoStore','TrackingOrderController@getOnlineInfoStore');

Route::POST('services/getOnlineStatus','TrackingOrderController@getOnlineStatusStore');
Route::GET('services/getOnlineStatus','TrackingOrderController@getOnlineStatusStore');

Route::POST('services/getAssignedOrders','TrackingOrderController@getAssignedOrders');
Route::GET('services/getAssignedOrders','TrackingOrderController@getAssignedOrders');

Route::POST('services/getInfoEmploye','TrackingOrderController@getInfoEmploye');
Route::GET('services/getInfoEmploye','TrackingOrderController@getInfoEmploye');

Route::POST('services/getAdminByEmail','TrackingOrderController@getAdminByEmail');
Route::GET('services/getAdminByEmail','TrackingOrderController@getAdminByEmail');

Route::POST('services/getAdminStores','TrackingOrderController@getAdminStores');
Route::GET('services/getAdminStores','TrackingOrderController@getAdminStores');

Route::POST('services/getAdminStoreById','TrackingOrderController@getAdminStoreById');
Route::GET('services/getAdminStoreById','TrackingOrderController@getAdminStoreById');

Route::POST('services/getActiveEmployeOnStore','TrackingOrderController@getActiveEmployeOnStore');
Route::GET('services/getActiveEmployeOnStore','TrackingOrderController@getActiveEmployeOnStore');

Route::POST('services/setDeliveredOrder','TrackingOrderController@setDeliveredOrder');
Route::GET('services/setDeliveredOrder','TrackingOrderController@setDeliveredOrder');

Route::POST('services/getInProccessOrders','TrackingOrderController@getInProccessOrders');
Route::GET('services/getInProccessOrders','TrackingOrderController@getInProccessOrders');

//loyalty program
Route::POST('services/changeLoyaltyPoints','LoyaltyProgramController@changeLoyaltyPoints');
Route::GET('services/changeLoyaltyPoints','LoyaltyProgramController@changeLoyaltyPoints');

Route::POST('services/addLoyaltyPoints','LoyaltyProgramController@addLoyaltyPoints');
Route::GET('services/addLoyaltyPoints','LoyaltyProgramController@addLoyaltyPoints');

Route::POST('services/calculateLoyaltyPoints','LoyaltyProgramController@calculateLoyaltyPoints');
Route::GET('services/calculateLoyaltyPoints','LoyaltyProgramController@calculateLoyaltyPoints');

Route::POST('services/getLoyaltyStatus','LoyaltyProgramController@getLoyaltyStatus');
Route::GET('services/getLoyaltyStatus','LoyaltyProgramController@getLoyaltyStatus');

Route::POST('services/getLoyaltyPromotions','LoyaltyProgramController@getLoyaltyPromotions');
Route::GET('services/getLoyaltyPromotions','LoyaltyProgramController@getLoyaltyPromotions');

Route::POST('services/getLoyaltyPromotionById','LoyaltyProgramController@getLoyaltyPromotionById');
Route::GET('services/getLoyaltyPromotionById','LoyaltyProgramController@getLoyaltyPromotionById');

