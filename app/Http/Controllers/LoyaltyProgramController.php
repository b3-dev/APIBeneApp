<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\Client;
use App\LoyaltyProgram;
use App\Store;
use App\Promotion;
use App\Product;
use App\Size;
use App\Ingredient;

class LoyaltyProgramController extends Controller
{
    //
    public function addLoyaltyPoints(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'addLoyaltyPoints') {

                        $dataValues = array(
                            ///newFields..
                            'id_cliente' => $data['data_loyalty']['id_cliente'],
                            'id_orden' => $data['data_loyalty']['id_orden'],
                            'fecha_nuevos_puntos_cliente' => date("Y-m-d H:i:s"),
                            'cantitad_nuevos_puntos_cliente' => intval($data['data_loyalty']['total_compra_cliente'] / 10),
                            'monto_compra_cliente' => $data['data_loyalty']['total_compra_cliente'],
                        );

                        $insert = LoyaltyProgram::addLoyaltyPoints($dataValues);

                        if ($insert > 0) {

                            $dataClient['client']['id_cliente'] = $data['data_loyalty']['id_cliente'];
                            $dataClient['client']['id_app_lealtad_nuevos_puntos_cliente'] = $insert;
                            $response['status'] = 'OK';
                            $response['data'] = $dataClient;
                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1005;
                            $dataWrong['error']['message'] = 'No se pudo insertar en la base de datos';
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

    public function calculateLoyaltyPoints(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'calculateLoyaltyPoints') {
                        if (!empty($data['data_loyalty']['amount'])) {
                            $amount = $data['data_loyalty']['amount'];
                            $points = intval($amount / 10);
                            $dataLoyalty['points'] = $points;
                            $dataLoyalty['amount'] = $amount;
                            $response['status'] = 'OK';
                            $response['data'] = $dataLoyalty;
                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1001;
                            $dataWrong['error']['message'] = 'Petición incorrecta';
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

    public function changeLoyaltyPoints(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'changeLoyaltyPoints') {

                        $dataValues = array(
                            ///newFields..
                            'id_cliente' => $data['data_loyalty']['id_cliente'],
                            'id_orden' => $data['data_loyalty']['id_orden'],
                            'fecha_canje_puntos_cliente' => date("Y-m-d H:i:s"),
                            'cantitad_canje_puntos_cliente' => $data['data_loyalty']['puntos_canje_cliente'], //puntos que canjeò
                            'descripcion_canje_puntos_cliente' => $data['data_loyalty']['descripcion_canje'],
                        );

                        $insert = LoyaltyProgram::changeLoyaltyPoints($dataValues);

                        if ($insert > 0) {

                            $dataClient['client']['id_cliente'] = $data['data_loyalty']['id_cliente'];
                            $dataClient['client']['id_app_lealtad_canje_puntos_cliente'] = $insert;
                            $response['status'] = 'OK';
                            $response['data'] = $dataClient;
                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1005;
                            $dataWrong['error']['message'] = 'No se pudo insertar en la base de datos';
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

    public function getLoyaltyStatus(Request $request) {
        // echo 'aca';
        try {

            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getLoyaltyStatus') {
                        //   $stores = Store::where('vigencia_unidad', 1)->where('id_unidad', $data['store']['id'])->get();
                        $params['id_unidad'] = $data['store']['id'];
                        $arrayConfigDSC = Store::getLoyaltyStatus($params);
                        if (count($arrayConfigDSC) > 0) {
                            $response['status'] = 'OK';
                            $response['data']['id_unidad'] = $data['store']['id'];
                            $response['data']['active_loyalty'] = $arrayConfigDSC[0]->activa_web_config;
                            return response()->json($response);
                        } else {
                            $response['status'] = 'OK';
                            $response['data']['id_unidad'] = $data['store']['id'];
                            $response['data']['active_loyalty'] = 0;
                            return response()->json($response);
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
    
    public function getLoyaltyPromotions(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getLoyaltyPromotions') {
                        //json..
                        $promotions = LoyaltyProgram::getLoyaltyPromotions();
                        $arrayPromotions = $this->parsingLoyaltyPromotions($promotions);
                        if (count($promotions) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $arrayPromotions;
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
    
    public function getLoyaltyPromotionById(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getLoyaltyPromotionById') {
                        //json..
                        if (!empty($data['promotion']['id'])) {
                            $dataPromotion['id_articulo'] = $data['promotion']['id'];
                            $promotions = LoyaltyProgram::getLoyaltyPromotionsById($dataPromotion);
                            $arrayPromotions = $this->parsingLoyaltyPromotions($promotions);
                            if (count($arrayPromotions) > 0) {
                                $response['status'] = 'OK';
                                $response['data'] = $arrayPromotions;
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
    
    public function parsingLoyaltyPromotions($array) {

        $rowTmp = array();
        $arrayTmp = array();

        $rowProductTmp = array();
        $arrayProductTmp = array();

        foreach ($array as $row) {
            $rowTmp['id_articulo'] = $row->id_articulo;
            $rowTmp['app_img_articulo'] = $row->app_img_articulo;
            $rowTmp['app_img_banner_articulo'] = $row->app_img_banner_articulo;
            $rowTmp['id_paquete_web_articulo'] = $row->id_paquete_web_articulo;
            $rowTmp['app_nombre_articulo'] = $row->app_nombre_articulo;
            $rowTmp['app_descripcion_articulo'] = $row->app_descripcion_articulo;
            $rowTmp['app_vigencia_articulo'] = $row->app_vigencia_articulo;
            $rowTmp['app_dia_vigencia_articulo'] = $row->app_dia_vigencia_articulo;
            $rowTmp['id_esquema_cobro_ventamaxx'] = $row->id_esquema_cobro_ventamaxx;
            $rowTmp['app_path_img_categoria'] = $row->app_path_img_categoria;
            $rowTmp['nombre_categoria'] = $row->nombre_categoria;
            $rowTmp['app_img_articulo'] = $row->app_img_articulo;

            $includedProducts = Promotion::getIncludesProductsByPromotion($row->id_paquete_web_articulo);
            unset($rowProductTmp);
            unset($arrayProductTmp);

            if (count($includedProducts) > 0) {

                $rowProductTmp = array();
                $arrayProductTmp = array();
                foreach ($includedProducts as $rowItems) {
                    $rowProductTmp['Item']['id_articulo'] = $rowItems->id_articulo;
                    $rowProductTmp['Item']['id_categoria'] = $rowItems->id_categoria;
                    $rowProductTmp['Item']['app_path_img_categoria'] = $rowItems->app_path_img_categoria;
                    $rowProductTmp['Item']['app_img_articulo'] = $rowItems->app_img_articulo;
                    $rowProductTmp['Item']['app_img_banner_articulo'] = $rowItems->app_img_banner_articulo;
                    // if($rowItems->id_subcategoria_articulo>0)
                    $rowProductTmp['Item']['id_subcategoria'] = $rowItems->id_subcategoria_articulo;

                    $rowProductTmp['Item']['app_nombre_articulo'] = $rowItems->app_nombre_articulo;
                    $rowProductTmp['Item']['app_descripcion_articulo'] = $rowItems->app_descripcion_articulo;
                    $rowProductTmp['Item']['cantidad_articulo'] = $rowItems->cantidad_articulo;

                    /* VERIFY IF IS PIZZA AND LIST OF INGREDIENTES OR SPECIALITY */
                    unset($rowProductTmp['Item']['MaxIngredients']);
                    unset($rowProductTmp['Item']['Ingredients']);
                    unset($rowProductTmp['Item']['Speciality']);
                    if ($rowItems->id_app_config_base_pizza_articulo > 0) {
                        $rowConfigPizza = Promotion::getConfigBasePizzaByPromotion($rowItems->id_app_config_base_pizza_articulo);
                        if (count($rowConfigPizza) > 0) {
                            $config_list_ing = trim($rowConfigPizza[0]->config_list_ingredientes_pizza_articulo);
                            $config_list_esp = trim($rowConfigPizza[0]->config_list_especialidades_pizza_articulo);

                            if (strlen($config_list_ing) > 0) {
                                if ($config_list_ing == '*') { //todos los ingredientes;
                                    //GET ALL LIST ING..
                                    $arrayIng = Ingredient::where('ban_display_ingrediente', 1)
                                            ->orderBy('prioridad_lista_ingrediente','ASC')
                                            ->get();

                                    $rowProductTmp['Item']['MaxIngredients'] = $rowConfigPizza[0]->config_limite_ingredientes_pizza_articulo;
                                    $rowProductTmp['Item']['Ingredients'] = $arrayIng;
                                    $rowProductTmp['Item']['Speciality'] = [];
                                }
                                else{
                                    if(!strstr($config_list_ing, ',') && intval($config_list_ing) > 0){
                                       
                                        $arrayIng = Ingredient::where('ban_display_ingrediente', 1)
                                            ->where('id_ingrediente_pizza',$config_list_ing)
                                            ->orderBy('prioridad_lista_ingrediente','ASC')
                                            ->get();

                                            $rowProductTmp['Item']['MaxIngredients'] = $rowConfigPizza[0]->config_limite_ingredientes_pizza_articulo;
                                            $rowProductTmp['Item']['Ingredients'] = $arrayIng;
                                            $rowProductTmp['Item']['Speciality'] = [];
                                    }
                                    elseif (strstr($config_list_ing, ',')) {
                                        $arrayExplode = explode(",", $config_list_ing);
                                        if (count($arrayExplode)) {
                                          
                                            $dataIngredient['arrayIngredients'] = $arrayExplode;
                                            $ingredients = Ingredient::getIngredientsByArray($dataIngredient);
                                            $rowProductTmp['Item']['MaxIngredients'] = $rowConfigPizza[0]->config_limite_ingredientes_pizza_articulo;
                                            $rowProductTmp['Item']['Ingredients'] = $ingredients;
                                            $rowProductTmp['Item']['Speciality'] = [];
                                        }
                                    }
                                }
                            } elseif (strlen($config_list_esp) > 0) {

                                if ($config_list_esp == '*') {
                                    $dataProduct['id_categoria'] = 1; //especialidades
                                    $speciality = Product::getProductsByCategory($dataProduct);
                                    $rowProductTmp['Item']['MaxIngredients'] = $rowConfigPizza[0]->config_limite_ingredientes_pizza_articulo;
                                    $rowProductTmp['Item']['Ingredients'] = [];
                                    $rowProductTmp['Item']['Speciality'] = $speciality;
                                }
                                elseif (strstr($config_list_esp, ',')) {
                                    $arrayExplode = explode(",", $config_list_esp);

                                    if (count($arrayExplode)) {
                                        $dataProduct['id_categoria'] = 1;
                                        $dataProduct['arraySpecialities'] = $arrayExplode;
                                        $speciality = Product::getProductsByArray($dataProduct);
                                        $rowProductTmp['Item']['MaxIngredients'] = $rowConfigPizza[0]->config_limite_ingredientes_pizza_articulo;
                                        $rowProductTmp['Item']['Ingredients'] = [];
                                        $rowProductTmp['Item']['Speciality'] = $speciality;
                                    }
                                }
                            }
                        }
                    }

                    $stringSizes = $rowItems->string_tamanos_articulo; //para productos que tengan el mismo precio..
                    $arraySizes = Size::where('id_tamano_articulo', intval($stringSizes))->get();
                    ///si son pizzas..
                    $arrayTmpSize = array();
                    $arrayTmpSize['id_tamano_articulo'] = intval($arraySizes[0]->id_tamano_articulo);
                    $arrayTmpSize['app_valor_porcion_tamano_articulo'] = intval($arraySizes[0]->app_valor_porcion_tamano_articulo);
                    $arrayTmpSize['web_descripcion_tamano_articulo'] = $arraySizes[0]->web_descripcion_tamano_articulo;
                    $arrayTmpSize['web_porcion_tamano_articulo'] = $arraySizes[0]->web_porcion_tamano_articulo;
                    
                    $arrayImgsizes=Size::getRelImgBySizeId($arrayTmpSize);
                    if(count($arrayImgsizes)>0){
                        $arrayTmpSize['app_path_img_bases_tamano_pizza'] = $arrayImgsizes[0]->app_path_img_bases_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_left_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_left_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_right_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_right_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_left_tamano_pizza'] = $arrayImgsizes[0]->app_path_img_bases_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_right_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_right_tamano_pizza;                     
                        $arrayTmpSize['app_img_base_orilla_rellena_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_tamano_pizza;
                        $arrayTmpSize['app_img_base_orilla_rellena_left_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_left_tamano_pizza;

                        $arrayTmpSize['app_img_base_orilla_rellena_right_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_right_tamano_pizza;
                        $arrayTmpSize['app_img_base_cruji_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_cruji_tamano_pizza;
                        $arrayTmpSize['app_img_base_cruji_left_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_cruji_left_tamano_pizza;
                        $arrayTmpSize['app_img_base_cruji_right_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_cruji_right_tamano_pizza;
                       
                        $arrayTmpSize['app_img_base_con_ajonjoli_leftTop4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_leftTop4x4_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_leftBottom4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_leftBottom4x4_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_rightTop4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_rightTop4x4_tamano_pizza;

                        $arrayTmpSize['app_img_base_con_ajonjoli_rightBottom4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_rightBottom4x4_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_leftTop4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_leftTop4x4_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_leftBottom4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_leftBottom4x4_tamano_pizza;

                        $arrayTmpSize['app_img_base_sin_ajonjoli_rightTop4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_rightTop4x4_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_rightBottom4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_rightBottom4x4_tamano_pizza;
                        $arrayTmpSize['app_img_base_orilla_rellena_leftTop4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_leftTop4x4_tamano_pizza;
                        
                        $arrayTmpSize['app_img_base_orilla_rellena_leftBottom4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_leftBottom4x4_tamano_pizza;
                        $arrayTmpSize['app_img_base_orilla_rellena_rightTop4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_rightTop4x4_tamano_pizza;
                        $arrayTmpSize['app_img_base_orilla_rellena_rightBottom4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_rightBottom4x4_tamano_pizza;
                    }                   
                    
                    $rowProductTmp['Item']['size'] = $arrayTmpSize;
                    array_push($arrayProductTmp, $rowProductTmp);
                }

                $rowTmp['Products'] = $arrayProductTmp;
            } else {
                unset($rowTmp['Products']);
            }

            $pricePromotion = Promotion::getPricePromotions($row->id_articulo);

            //   dd($pricePromotion);
            if (count($pricePromotion) > 0) {
                $rowTmp['Points'] = $pricePromotion[0]->puntos_articulo_tamano;
            } else {
                unset($rowTmp['Points']);
            }
            array_push($arrayTmp, $rowTmp);
        } //fin del for..

        return $arrayTmp;
    }

}
