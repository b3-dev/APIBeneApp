<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
Use Illuminate\Support\Facades\Config;
use App\Ingredient;


class IngredientsController extends Controller
{
    //
    
    public function parsingArrayIngredients($ingredients) {

        $rowTmp = array();
        $arrayTmp = array();
        foreach ($ingredients as $ingredient) {
            $rowTmp = $ingredient;
            $data['id_ingrediente_pizza'] = $ingredient->id_ingrediente_pizza;
            $ArrayRelSizeIngredient = Ingredient::getRelImgSizeIngredientByd($data);
            $rowTmp['data_images_by_sizes'] = $ArrayRelSizeIngredient;
            $rowTmp['data_prices_by_sizes'] = Ingredient::getRelPricesByIngredient($data);

            array_push($arrayTmp, $rowTmp);
        }

        return $arrayTmp;
    }

    public function parsingArrayIngredientsBySpeciality($ingredients) {

        $rowTmp = array();
        $arrayTmp = array();
        foreach ($ingredients as $ingredient) {

            $rowTmp['id_rel_ingrediente_articulo'] = $ingredient->id_rel_ingrediente_articulo;
            $rowTmp['id_articulo'] = $ingredient->id_articulo;
            $rowTmp['id_ingrediente_pizza'] = $ingredient->id_ingrediente_pizza;
            $rowTmp['precio_ingrediente_articulo'] = $ingredient->precio_ingrediente_articulo;
            $rowTmp['descripcion_ingrediente_pizza'] = $ingredient->descripcion_ingrediente_pizza;
            $rowTmp['id_categoria_ingrediente'] = $ingredient->id_categoria_ingrediente;
            $rowTmp['app_path_img_ingrediente_pizza'] = $ingredient->app_path_img_ingrediente_pizza;
            $rowTmp['app_img_icon_ingrediente_pizza'] = $ingredient->app_img_icon_ingrediente_pizza;
            $rowTmp['web_img_ingrediente_pizza'] = $ingredient->web_img_ingrediente_pizza;
            $rowTmp['id_receta_ventamaxx'] = $ingredient->id_receta_ventamaxx;
            $rowTmp['ban_display_ingrediente'] = $ingredient->ban_display_ingrediente;
            $rowTmp['prioridad_lista_ingrediente'] = $ingredient->prioridad_lista_ingrediente;
            $rowTmp['app_prioridad_armado_pizza_ingrediente'] = $ingredient->app_prioridad_armado_pizza_ingrediente;

            $data['id_ingrediente_pizza'] = $ingredient->id_ingrediente_pizza;
            $ArrayRelSizeIngredient = Ingredient::getRelImgSizeIngredientByd($data);
            $rowTmp['data_images_by_sizes'] = $ArrayRelSizeIngredient;
            $rowTmp['data_prices_by_sizes'] = Ingredient::getRelPricesByIngredient($data);

            array_push($arrayTmp, $rowTmp);
        }

        return $arrayTmp;
    }

    public function ingredients(Request $request) {
       // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getIngredients') {

                        $ingredients = Ingredient::where('ban_display_ingrediente', 1)->orderBy('prioridad_lista_ingrediente','ASC')->get();
                        $arrayFullIngredients=$this->parsingArrayIngredients($ingredients);          
                        
                        if (count($arrayFullIngredients) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $arrayFullIngredients;
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
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
    
    public function getIngredientById(Request $request) {
       // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getIngredientById') {

                        $ingredient = Ingredient::where('id_ingrediente_pizza',$data['ingredient']['id'] )->get();
                        $arrayFullIngredients=$this->parsingArrayIngredients($ingredient);
                        if (count($arrayFullIngredients) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $arrayFullIngredients;
                            return response()->json($response);
                        }
                       /* if (count($ingredient) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $ingredient;
                            return response()->json($response);
                        }*/ else {
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
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
      
    public function getIngredientByIdCategory(Request $request) {
       // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getIngredientByIdCategory') {

                        $ingredient = Ingredient::where('id_categoria_ingrediente',$data['category_ingredient']['id'] )
                                ->where('ban_display_ingrediente',1)
                                ->get();
                        if (count($ingredient) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $ingredient;
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
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
    
    public function getIngredientByIdRate(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getIngredientByIdRate') {

                        $ingredients = Ingredient::where('id_tipo_cobro_ingrediente', $data['rate']['id'])
                                ->where('ban_display_ingrediente', 1)
                                ->get();
                        $arrayFullIngredients = $this->parsingArrayIngredients($ingredients);


                        if (count($arrayFullIngredients) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $arrayFullIngredients;
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
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }

    public function getCategoryIngredients(Request $request) {
       // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getCategoryIngredients') {

                        $ingredient = Ingredient::getCategoryIngredients();
                        if (count($ingredient) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $ingredient;
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
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
    
    public function getCategoryIngredientById(Request $request) {
       // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getCategoryIngredientById') {

                        $category_ingredient = Ingredient::getCategoryIngredientById($data['category_ingredient']['id']);
                        if (count($category_ingredient) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $category_ingredient;
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
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }
    
    public function getExtrachessePrice(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');

            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getExtrachessePrice') {
                        //json..
                        $dataProduct['id_ingrediente_pizza'] = 21; //extrachesse
                        $dataProduct['id_tamano_articulo'] = $data['size']['id'];
                        $extrachesse = Ingredient::getExtrachessePrice($dataProduct);
                        if (count($extrachesse) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $extrachesse;
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

    public function getChesseBorderPrice(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');

            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getChesseBorderPrice') {
                        //json..
                        $dataProduct['id_ingrediente_pizza'] =45;
                        $dataProduct['id_tamano_articulo'] = $data['size']['id'];
                        $extrachesse = Ingredient::getChesseBorderPrice($dataProduct);
                        if(count($extrachesse)>0){
                            $response['status'] = 'OK';
                            $response['data'] = $extrachesse;
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
    
    public function getPanPizzaPrice(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');

            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getPanPizzaPrice') {
                        //json..
                        $dataProduct['id_ingrediente_pizza'] =49;
                        $dataProduct['id_tamano_articulo'] = $data['size']['id'];
                        $extrachesse = Ingredient::getPanpizzaPrice($dataProduct);
                        if(count($extrachesse)>0){
                            $response['status'] = 'OK';
                            $response['data'] = $extrachesse;
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
    
    public function getIngredientsBySpecialty(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');

            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getIngredientsBySpecialty') {
                        //json..
                        $dataProduct['id_articulo'] =$data['product']['id'];
                        $ingredients = Ingredient::getIngredientsBySpecialty($dataProduct);
                        $arrayFullIngredients=$this->parsingArrayIngredientsBySpeciality($ingredients);
                        
                        
                        if(count($arrayFullIngredients)>0){
                            $response['status'] = 'OK';
                            $response['data'] = $arrayFullIngredients;
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
    
    public function getChesseBorderItems(Request $request) {
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getChesseBorderItems') {

                        $chesseBorderItems = Ingredient::getChesseBorderItems();
                        //$arrayFullIngredients=$this->parsingArrayIngredients($ingredients);          

                        if (count($chesseBorderItems) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $chesseBorderItems;
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
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }

}
