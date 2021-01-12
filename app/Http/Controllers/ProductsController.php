<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\Product;

class ProductsController extends Controller
{
    //
    
    public function parsinProductsAndRelImg($param) {

        $rowTmp = array();
        $arrayTmp = array();
        $arrayProduct=$param['products'];

        foreach ($arrayProduct as $product) {
            $rowTmp['id_articulo'] = $product->id_articulo;
            $rowTmp['nombre_articulo'] = $product->nombre_articulo;
            $rowTmp['app_nombre_articulo'] = $product->app_nombre_articulo;
            $rowTmp['app_descripcion_articulo'] = $product->app_descripcion_articulo;
            $rowTmp['id_categoria_promocion'] = $product->id_categoria_promocion;
            $rowTmp['app_vigencia_articulo'] = $product->app_vigencia_articulo;
            $rowTmp['app_dia_vigencia_articulo'] = $product->app_dia_vigencia_articulo;
            $rowTmp['fecha_vigencia_promocion'] = $product->fecha_vigencia_promocion;
            $rowTmp['ban_display_in_list'] = $product->ban_display_in_list;
            $rowTmp['vigencia_articulo'] = $product->vigencia_articulo;
           
            $rowTmp['web_nombre_articulo'] = $product->web_nombre_articulo;
            $rowTmp['descripcion_articulo'] = $product->descripcion_articulo;
            $rowTmp['web_img_articulo'] = $product->web_img_articulo;
            $rowTmp['app_path_img_categoria'] = $product->app_path_img_categoria;

            $rowTmp['app_img_articulo'] = $product->app_img_articulo;
            $rowTmp['app_img_miniatura'] = $product->app_img_miniatura;
            $rowTmp['web_img_articulo_modificada'] = $product->web_img_articulo_modificada;
            $rowTmp['id_categoria'] = $product->id_categoria;
            $rowTmp['prioridad_orden_articulo'] = $product->prioridad_orden_articulo;
            $rowTmp['web_img_articulo_big'] = $product->web_img_articulo_big;
            $rowTmp['app_img_articulo_big'] = $product->app_img_articulo_big;
            $rowTmp['id_paquete_web_articulo'] = $product->id_paquete_web_articulo;
            $rowTmp['contiene_pizza_paquete'] = $product->contiene_pizza_paquete;
            $rowTmp['valida_esp_paquete'] = $product->valida_esp_paquete;
            $rowTmp['id_subcategoria'] = $product->id_subcategoria;
            $rowTmp['nombre_categoria'] = $product->nombre_categoria;
            $rowTmp['app_prioridad_categoria'] = $product->app_prioridad_categoria;
            $rowTmp['app_vigencia_categoria'] = $product->app_vigencia_categoria;

            $data['id_articulo']=$product->id_articulo;
            $data['id_unidad']=$param['id_unidad'];
            $arrayImgSizeProduct = Product::getRelImgSizeProductId($data);
            $arraySizes = Product::getProductSizes($data);
            $rowTmp['data_images_by_sizes'] = $arrayImgSizeProduct;
            $dataProduct['id_articulo'] = $product->id_articulo;
            $nutritionalInfo = Product::getNutritionalInfoById($dataProduct);
            $rowTmp['data_nutritional_info'] = $nutritionalInfo;         
            $rowTmp['sizes'] =  $arraySizes;
            
            
            array_push($arrayTmp, $rowTmp);
        }

        return $arrayTmp;
    }
    
    
    public function generateProductSuggested($data) {

        try {

            $Constans = Config::get('constants.options');
            @$rowProduct = Product::where('id_articulo', $data['id_articulo'])->get();
            $dataProduct=array();
            if (count($rowProduct)) {

                if ($rowProduct[0]['id_categoria'] == $Constans['POSTRES']) {
                    //bebida..
                    //$item['id_articulo'] = 15; //Mirinda..
                    $item['id_categoria'] = 5; //BAGUETTE
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct1 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    $item['id_categoria'] = 4; //BEBIDA
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    array_push($dataProduct,$rowProduct1[0]);
                    array_push($dataProduct,$rowProduct2[0]);

                    
                } elseif ($rowProduct[0]['id_categoria'] == $Constans['BAGUETTES']) {
                    //bebida..
                    //$item['id_articulo'] = 15; //Mirinda..
                    $item['id_categoria'] = 4; //BEBIDA
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct1 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    $item['id_categoria'] = 3; //POSTRE
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    array_push($dataProduct,$rowProduct1[0]);
                    array_push($dataProduct,$rowProduct2[0]);
                    
                } elseif ($rowProduct[0]['id_categoria'] == $Constans['ESPECIALIDADES']) {
                    //bebida..
                    $item['id_articulo'] = 146;
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct1 = Product::getRandomProductPerStore($item); //CHIMMI NORMAL.
                    $item['id_categoria'] = 3; //POSTRE
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    array_push($dataProduct,$rowProduct1[0]);
                    array_push($dataProduct,$rowProduct2[0]);
                    
                    
                } elseif ($rowProduct[0]['id_categoria'] == $Constans['PIZZAS']) {
                    //bebida..
                    $item['id_articulo'] = 146;
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct1 = Product::getRandomProductPerStore($item); //CHIMMI VERDE.
                    $item['id_categoria'] = 3; //POSTRE
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    array_push($dataProduct,$rowProduct1[0]);
                    array_push($dataProduct,$rowProduct2[0]);
                } elseif ($rowProduct[0]['id_categoria'] == $Constans['BEBIDAS']) {
                    //$item['id_articulo'] = 15; //Mirinda..
                    $item['id_categoria'] = 5; //BAGUETTE
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct1 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    $item['id_categoria'] = 3; //POSTRE
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    array_push($dataProduct,$rowProduct1[0]);
                    array_push($dataProduct,$rowProduct2[0]);
                    
                } elseif ($data['id_articulo'] == 1) { //FINGERS
                    //bebida..
                    //$item['id_articulo'] = 15; //Mirinda..
                    $item['id_articulo'] = 145;
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct1 = Product::getRandomProductPerStore($item); //CHIMMI.
                    $item['id_categoria'] = 4; //BEBIDA
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    array_push($dataProduct,$rowProduct1[0]);
                    array_push($dataProduct,$rowProduct2[0]);
                    // dd($dataProduct);
                } elseif ($data['id_articulo'] == 2) {//papachas
                    //bebida..
                    //$item['id_articulo'] = 15; //Mirinda..
                    $item['id_articulo'] = 145;
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct1 = Product::getRandomProductPerStore($item); //CHIMMI.
                    $item['id_categoria'] = 4; //BEBIDA
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    array_push($dataProduct,$rowProduct1[0]);
                    array_push($dataProduct,$rowProduct2[0]);
                    
                } else if ($data['id_articulo'] == 3) {//nuggets
                    $item['id_articulo'] = 145;
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct1 = Product::getRandomProductPerStore($item); //CHIMMI.
                    $item['id_categoria'] = 4; //BEBIDA
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    array_push($dataProduct,$rowProduct1[0]);
                    array_push($dataProduct,$rowProduct2[0]);
                    
                } else if ($data['id_articulo'] == 4) { //wings
                    $item['id_articulo'] = 145;
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct1 = Product::getRandomProductPerStore($item); //CHIMMI.
                    $item['id_categoria'] = 4; //BEBIDA
                    $item['id_unidad']=$data['id_unidad'];
                    $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    array_push($dataProduct,$rowProduct1[0]);
                    array_push($dataProduct,$rowProduct2[0]);
                } else {
                    //bebida..
                    //$item['id_articulo'] = 15; //Mirinda..
                    $item['id_categoria'] = 4; //ELSE BEBIDA
                    $item['id_unidad'] = $data['id_unidad'];
                    $rowProduct1 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    $item['id_categoria'] = 5; //BAGUETTE
                    $item['id_unidad'] = $data['id_unidad'];
                    $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                    array_push($dataProduct, $rowProduct1[0]);
                    array_push($dataProduct, $rowProduct2[0]);
                }
            } else {
                $item['id_categoria'] = 4; //ELSE BEBIDA
                $item['id_unidad'] = $data['id_unidad'];
                $rowProduct1 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                $item['id_categoria'] = 5; //BAGUETTE
                $item['id_unidad'] = $data['id_unidad'];
                $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
                array_push($dataProduct, $rowProduct1[0]);
                array_push($dataProduct, $rowProduct2[0]);
            }

            return $dataProduct;
        } catch (Exception $ex) {

            $item['id_categoria'] = 4; //ELSE BEBIDA
            $item['id_unidad'] = $data['id_unidad'];
            $rowProduct1 = Product::getRandomProductbyCategoryPerStore($item); //wings..
            $item['id_categoria'] = 5; //BAGUETTE
            $item['id_unidad'] = $data['id_unidad'];
            $rowProduct2 = Product::getRandomProductbyCategoryPerStore($item); //wings..
            array_push($dataProduct, $rowProduct1[0]);
            array_push($dataProduct, $rowProduct2[0]);
            return $dataProduct;
        }
    }

    public function productSuggestion(Request $request){
       // echo 'aca';
        $data = $request->json()->all();
        $Constans = Config::get('constants.options');
        
        if (count($data) > 0) {
            if ($data['token']['id'] == $Constans['API_KEY']) {

                if ($data['action'] == 'productSuggestion') {
                    
                    $product['id_articulo']=$data['product']['id'];
                    $product['id_tamano_articulo']=$data['product']['size_id'];
                    $product['id_unidad']=(!empty($data['store']['id']))?$data['store']['id']:$Constans['UNIDAD_MENU_SEVILLA'];
                    
                    $productSuggested = $this->generateProductSuggested($product);
                    if (count($productSuggested) > 0) {
                        $response['status'] = 'OK';
                        $response['data'] = $productSuggested;
                        return response()->json($response);
                    } else {

                        $dataWrong['status'] = 'error';
                        $dataWrong['error']['code'] = 1003;
                        $dataWrong['error']['message'] = 'Item(s) no localizado(s)';

                        return response()->json($dataWrong);
                    }
                } else {
                    $dataWrong['status'] = 'error';
                    $dataWrong['error']['code'] = 1001;
                    $dataWrong['error']['message'] = 'Petición incorrecta';
                    //header('Content-Type: application/json');
                    return response()->json($dataWrong);
                }

            } else {
                $dataWrong['status'] = 'error';
                $dataWrong['error']['code'] = 1004;
                $dataWrong['error']['message'] = 'Token incorrecto';
                //header('Content-Type: application/json');

                return response()->json($dataWrong);
            }
        } else {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1001;
            $dataWrong['error']['message'] = 'Petición incorrecta';
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
  
    public function products(Request $request){
       // echo 'aca';
        $data = $request->json()->all();
        $Constans = Config::get('constants.options');
        
        if (count($data) > 0) {
            if ($data['token']['id'] == $Constans['API_KEY']) {

                if ($data['action'] == 'getProducts') {
                    $dataStore['id_unidad']=(!empty($data['store']['id']))?$data['store']['id']:$Constans['UNIDAD_MENU_SEVILLA'];
                    $products = Product::getProductsPerStore($dataStore);
                    if (count($products) > 0) {
                        $response['status'] = 'OK';
                        $response['data'] = $products;
                        return response()->json($response);
                    } else {

                        $dataWrong['status'] = 'error';
                        $dataWrong['error']['code'] = 1003;
                        $dataWrong['error']['message'] = 'Item(s) no localizado(s)';

                        return response()->json($dataWrong);
                    }
                } else {
                    $dataWrong['status'] = 'error';
                    $dataWrong['error']['code'] = 1001;
                    $dataWrong['error']['message'] = 'Petición incorrecta';
                    //header('Content-Type: application/json');
                    return response()->json($dataWrong);
                }

            } else {
                $dataWrong['status'] = 'error';
                $dataWrong['error']['code'] = 1004;
                $dataWrong['error']['message'] = 'Token incorrecto';
                //header('Content-Type: application/json');

                return response()->json($dataWrong);
            }
        } else {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1001;
            $dataWrong['error']['message'] = 'Petición incorrecta';
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }

    public function getProductsByCategory(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');

            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..UNIDAD_MENU_REPARTO
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getProductsByCategory') {
                        //json..
                        $dataProduct['id_categoria'] = $data['category']['id'];
                        $dataProduct['id_unidad'] = (!empty($data['store']['id']))?$data['store']['id']:$Constans['UNIDAD_MENU_SEVILLA'];
                      
                        if (!empty($data['subcategory']['id']) && intval($data['subcategory']['id'] > 0)) {
                            
                            $dataProduct['id_subcategoria'] = $data['subcategory']['id'];                          
                            $products = Product::getProductsByCategoryAndSubcategoryPerStore($dataProduct);
                        } else {
                            $products = Product::getProductsByCategoryPerStore($dataProduct);
                            
                        }
                        
                        $dataProduct['products']=$products;
                        $arrayProducts = $this->parsinProductsAndRelImg($dataProduct);
                                               
                        if (count($arrayProducts) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $arrayProducts;
                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1003;
                            $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                            return response()->json($dataWrong);
                        }
                    } else {
                        $dataWrong['status'] = 'error';
                        $dataWrong['error']['code'] = 1001;
                        $dataWrong['error']['message'] = 'Petición incorrecta';
                        return response()->json($dataWrong);
                    }
                } else {
                    $dataWrong['status'] = 'error';
                    $dataWrong['error']['code'] = 1004;
                    $dataWrong['error']['message'] = 'Token incorrecto';
                    return response()->json($dataWrong);
                }
            } else {
                $dataWrong['status'] = 'error';
                $dataWrong['error']['code'] = 1001;
                $dataWrong['error']['message'] = 'Petición incorrecta';
                return response()->json($dataWrong);
            }
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
    
    public function getProductById(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');

            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getProductById') {
                        //json..
                        $dataProduct['id_articulo'] = $data['product']['id'];
                        $products = Product::getProductById($dataProduct);
                                              
                        if (count($products) > 0) {
                            $nutritionalInfo = Product::getNutritionalInfoById($dataProduct);
                            $products['data_nutritional_info'] = $nutritionalInfo;

                            $response['status'] = 'OK';
                            $response['data'] = $products;

                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1003;
                            $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                            return response()->json($dataWrong);
                        }
                    } else {
                        $dataWrong['status'] = 'error';
                        $dataWrong['error']['code'] = 1001;
                        $dataWrong['error']['message'] = 'Petición incorrecta';
                        return response()->json($dataWrong);
                    }
                } else {
                    $dataWrong['status'] = 'error';
                    $dataWrong['error']['code'] = 1004;
                    $dataWrong['error']['message'] = 'Token incorrecto';
                    return response()->json($dataWrong);
                }
            } else {
                $dataWrong['status'] = 'error';
                $dataWrong['error']['code'] = 1001;
                $dataWrong['error']['message'] = 'Petición incorrecta';
                return response()->json($dataWrong);
            }
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
    
    public function getProductsByString(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');

            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getProductsByString') {
                        //json..
                        $dataProduct['name'] = $data['product']['name'];
                        $products = Product::getProductsByString($dataProduct);
                                              
                        if (count($products) > 0) {
                            
                          //  $nutritionalInfo = Product::getNutritionalInfoById($dataProduct);
                            //$products['data_nutritional_info'] = $nutritionalInfo;

                            $response['status'] = 'OK';
                            $response['data'] = $products;
                            
                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1003;
                            $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                            return response()->json($dataWrong);
                        }
                    } else {
                        $dataWrong['status'] = 'error';
                        $dataWrong['error']['code'] = 1001;
                        $dataWrong['error']['message'] = 'Petición incorrecta';
                        return response()->json($dataWrong);
                    }
                } else {
                    $dataWrong['status'] = 'error';
                    $dataWrong['error']['code'] = 1004;
                    $dataWrong['error']['message'] = 'Token incorrecto';
                    return response()->json($dataWrong);
                }
            } else {
                $dataWrong['status'] = 'error';
                $dataWrong['error']['code'] = 1001;
                $dataWrong['error']['message'] = 'Petición incorrecta';
                return response()->json($dataWrong);
            }
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
    
    public function getProducSizes(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getProducSizes') {
                        //json..
                        $dataProduct['id_articulo'] = $data['product']['id'];
                        $dataProduct['id_unidad'] = (!empty($data['store']['id']))?$data['store']['id']:$Constans['UNIDAD_MENU_SEVILLA'];
                        $products = Product::getProductById($dataProduct);
                        $dataProduct['arrayProducts']=$products;
                        
                        $productSizes = $this->pasingProductAndSizes($dataProduct);
                        
                        if (count($productSizes) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $productSizes;
                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1003;
                            $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                            return response()->json($dataWrong);
                        }
                    } else {
                        $dataWrong['status'] = 'error';
                        $dataWrong['error']['code'] = 1001;
                        $dataWrong['error']['message'] = 'Petición incorrecta';
                        return response()->json($dataWrong);
                    }
                } else {
                    $dataWrong['status'] = 'error';
                    $dataWrong['error']['code'] = 1004;
                    $dataWrong['error']['message'] = 'Token incorrecto';
                    return response()->json($dataWrong);
                }
            } else {
                $dataWrong['status'] = 'error';
                $dataWrong['error']['code'] = 1001;
                $dataWrong['error']['message'] = 'Petición incorrecta';
                return response()->json($dataWrong);
            }
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }

    public function getProductPrice(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');

            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getProductPrice') {
                        //json..
                        $dataProduct['id_articulo'] = $data['product']['id'];
                        $dataProduct['id_tamano_articulo'] = $data['product']['size_id'];
                        $dataProduct['id_unidad'] = (!empty($data['store']['id']))?$data['store']['id']:$Constans['UNIDAD_MENU_SEVILLA'];
                        $products = Product::getProductPricePerStore($dataProduct);
                        if(count($products)>0){
                            $response['status'] = 'OK';
                            $response['data'] = $products;
                            return response()->json($response);
                        }
                        else{
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1003;
                            $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                            return response()->json($dataWrong);
                        }                       
                        
                    } else {
                        $dataWrong['status'] = 'error';
                        $dataWrong['error']['code'] = 1001;
                        $dataWrong['error']['message'] = 'Petición incorrecta';
                        return response()->json($dataWrong);
                    }
                } else {
                    $dataWrong['status'] = 'error';
                    $dataWrong['error']['code'] = 1004;
                    $dataWrong['error']['message'] = 'Token incorrecto';
                    return response()->json($dataWrong);
                }
            } else {
                $dataWrong['status'] = 'error';
                $dataWrong['error']['code'] = 1001;
                $dataWrong['error']['message'] = 'Petición incorrecta';
                return response()->json($dataWrong);
            }
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
    
    public function getPriceByCategoryAndSize(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');

            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getPriceByCategoryAndSize') {
                        //json..
                        $dataProduct['id_categoria'] = $data['category']['id'];
                        $dataProduct['id_tamano_articulo'] = $data['size']['id'];
                        $dataProduct['id_unidad'] = (!empty($data['store']['id']))?$data['store']['id']:$Constans['UNIDAD_MENU_SEVILLA'];

                        if (!empty($data['subcategory']['id']) && intval($data['subcategory']['id'])>0 ) {
                            $dataProduct['id_subcategoria'] = $data['subcategory']['id'];
                            $products = Product::getPriceByCategorieSubcategoryAndSizePerStore($dataProduct);
                        } else {
                            $products = Product::getPriceByCategorieAndSizePerStore($dataProduct);
                        }

                        if (count($products) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $products;
                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1003;
                            $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                            return response()->json($dataWrong);
                        }
                    } else {
                        $dataWrong['status'] = 'error';
                        $dataWrong['error']['code'] = 1001;
                        $dataWrong['error']['message'] = 'Petición incorrecta';
                        return response()->json($dataWrong);
                    }
                } else {
                    $dataWrong['status'] = 'error';
                    $dataWrong['error']['code'] = 1004;
                    $dataWrong['error']['message'] = 'Token incorrecto';
                    return response()->json($dataWrong);
                }
            } else {
                $dataWrong['status'] = 'error';
                $dataWrong['error']['code'] = 1001;
                $dataWrong['error']['message'] = 'Petición incorrecta';
                return response()->json($dataWrong);
            }
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }

    public function getPizza2IngPrice(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');

            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getPizza2IngPrice') {
                        //json..
                        $dataProduct['id_articulo'] =36;
                        $dataProduct['id_tamano_articulo'] = $data['size']['id'];
                        $products = Product::getProductPrice($dataProduct);
                        if(count($products)>0){
                            $response['status'] = 'OK';
                            $response['data'] = $products;
                            return response()->json($response);
                        }
                        else{
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1003;
                            $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                            return response()->json($dataWrong);
                        }                       
                        
                    } else {
                        $dataWrong['status'] = 'error';
                        $dataWrong['error']['code'] = 1001;
                        $dataWrong['error']['message'] = 'Petición incorrecta';
                        return response()->json($dataWrong);
                    }
                } else {
                    $dataWrong['status'] = 'error';
                    $dataWrong['error']['code'] = 1004;
                    $dataWrong['error']['message'] = 'Token incorrecto';
                    return response()->json($dataWrong);
                }
            } else {
                $dataWrong['status'] = 'error';
                $dataWrong['error']['code'] = 1001;
                $dataWrong['error']['message'] = 'Petición incorrecta';
                return response()->json($dataWrong);
            }
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
    
    public function getPizzaEspPrice(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');

            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getPizzaEspPrice') {
                        //json..
                        $dataProduct['id_articulo'] = 37;
                        $dataProduct['id_tamano_articulo'] = $data['size']['id'];
                        $products = Product::getProductPrice($dataProduct);
                        if (count($products) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $products;
                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1003;
                            $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                            return response()->json($dataWrong);
                        }
                    } else {
                        $dataWrong['status'] = 'error';
                        $dataWrong['error']['code'] = 1001;
                        $dataWrong['error']['message'] = 'Petición incorrecta';
                        return response()->json($dataWrong);
                    }
                } else {
                    $dataWrong['status'] = 'error';
                    $dataWrong['error']['code'] = 1004;
                    $dataWrong['error']['message'] = 'Token incorrecto';
                    return response()->json($dataWrong);
                }
            } else {
                $dataWrong['status'] = 'error';
                $dataWrong['error']['code'] = 1001;
                $dataWrong['error']['message'] = 'Petición incorrecta';
                return response()->json($dataWrong);
            }
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
    
    public function pasingProductAndSizes($dataProduct) {
        $product=$dataProduct['arrayProducts'];
        $rowTmp = array();
        $arrayTmp = array();
        $rowSize = array();
        $arraySizes = array();
       
            $rowTmp['id_articulo'] = $product[0]->id_articulo;
            $rowTmp['nombre_articulo'] = $product[0]->nombre_articulo;
            $rowTmp['app_nombre_articulo'] = $product[0]->app_nombre_articulo;
            $rowTmp['app_descripcion_articulo'] = $product[0]->app_descripcion_articulo;
            $rowTmp['app_vigencia_articulo'] = $product[0]->app_vigencia_articulo;
            $rowTmp['app_dia_vigencia_articulo'] = $product[0]->app_dia_vigencia_articulo;
            $rowTmp['fecha_vigencia_promocion'] = $product[0]->fecha_vigencia_promocion;
            $rowTmp['ban_display_in_list'] = $product[0]->ban_display_in_list;
            $rowTmp['vigencia_articulo'] = $product[0]->vigencia_articulo;
            $rowTmp['id_art_receta_ventamaxx'] = $product[0]->id_art_receta_ventamaxx;
            $rowTmp['id_art_tamanno_ventamaxx'] = $product[0]->id_art_tamanno_ventamaxx;
            $rowTmp['web_nombre_articulo'] = $product[0]->web_nombre_articulo;
            $rowTmp['descripcion_articulo'] = $product[0]->descripcion_articulo;
            $rowTmp['id_categoria'] = $product[0]->id_categoria;
            $rowTmp['app_img_articulo'] = $product[0]->app_img_articulo;
            $rowTmp['app_img_articulo_big'] = $product[0]->app_img_articulo_big;
            $rowTmp['id_subcategoria'] = $product[0]->id_subcategoria;
            $rowTmp['id_unidad']= $dataProduct['id_unidad'];
            $Sizes = Product::getProductSizesPerStore($rowTmp);
            if (count($Sizes) > 0) {
                foreach ($Sizes as $Size) {
                  
                    $rowSize['id_tamano_articulo'] = $Size->id_tamano_articulo;
                    $rowSize['prioridad_orden_articulo'] = $Size->prioridad_orden_articulo;
                    $rowSize['descripcion_tamano_articulo'] = $Size->descripcion_tamano_articulo;
                    $rowSize['web_descripcion_tamano_articulo'] = $Size->web_descripcion_tamano_articulo;
                    $rowSize['precio_articulo_tamano'] = $Size->precio_articulo_tamano;
                    $rowSize['precio_dsc_articulo_tamano'] = $Size->precio_dsc_articulo_tamano;
                    $rowSize['ban_activo_dsc_articulo_tamano'] = $Size->ban_activo_dsc_articulo_tamano;
                    $rowSize['id_categoria'] = $Size->id_categoria;
                    $rowSize['app_vigencia_tamano_articulo'] = $Size->app_vigencia_tamano_articulo;

                    array_push($arraySizes, $rowSize);
                }

                $rowTmp['sizes'] = $arraySizes;
            } else
                $rowTmp['sizes'] = '';


         
     
        return $rowTmp;
    }

}
