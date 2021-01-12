<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\Promotion;
use App\Ingredient;
use App\Product;
use App\Size;


class PromotionsController extends Controller
{
    //
    public function parsingPromotions($array) {
        $Constans = Config::get('constants.options');
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
                                elseif (intval($config_list_esp)>0){
                                        $dataProduct['id_categoria'] = 1;
                                        $dataProduct['id_articulo'] = intval($config_list_esp);
                                        $speciality = Product::getProductByParamId($dataProduct);
                                        $rowProductTmp['Item']['MaxIngredients'] = $rowConfigPizza[0]->config_limite_ingredientes_pizza_articulo;
                                        $rowProductTmp['Item']['Ingredients'] = [];
                                        $rowProductTmp['Item']['Speciality'] = $speciality;
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

                        $arrayTmpSize['app_img_base_orilla_rellena_rightBottom4x4_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_rightBottom4x4_tamano_pizza;
                        /*octavos*/
                        $arrayTmpSize['app_img_base_pizza_doble_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_pizza_doble_tamano_pizza;

                        $arrayTmpSize['app_img_base_pizza_doble_1_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_pizza_doble_1_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_pizza_doble_2_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_pizza_doble_2_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_pizza_doble_3_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_pizza_doble_3_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_pizza_doble_4_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_pizza_doble_4_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_pizza_doble_5_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_pizza_doble_5_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_pizza_doble_6_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_pizza_doble_6_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_pizza_doble_7_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_pizza_doble_7_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_pizza_doble_8_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_pizza_doble_8_8_tamano_pizza;

                        $arrayTmpSize['app_img_base_con_ajonjoli_1_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_1_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_2_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_2_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_3_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_3_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_4_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_4_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_5_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_5_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_6_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_6_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_7_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_7_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_con_ajonjoli_8_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_con_ajonjoli_8_8_tamano_pizza;

                        $arrayTmpSize['app_img_base_sin_ajonjoli_1_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_1_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_2_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_2_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_3_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_3_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_4_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_4_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_5_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_5_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_6_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_6_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_7_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_7_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_sin_ajonjoli_8_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_sin_ajonjoli_8_8_tamano_pizza;

                        $arrayTmpSize['app_img_base_orilla_rellena_1_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_1_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_orilla_rellena_2_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_2_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_orilla_rellena_3_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_3_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_orilla_rellena_4_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_4_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_orilla_rellena_5_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_5_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_orilla_rellena_6_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_6_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_orilla_rellena_7_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_7_8_tamano_pizza;
                        $arrayTmpSize['app_img_base_orilla_rellena_8_8_tamano_pizza'] = $arrayImgsizes[0]->app_img_base_orilla_rellena_8_8_tamano_pizza;
                    }                   
                    
                    $rowProductTmp['Item']['size'] = $arrayTmpSize;
                    array_push($arrayProductTmp, $rowProductTmp);
                }

                $rowTmp['Products'] = $arrayProductTmp;
            } else {
                unset($rowTmp['Products']);
            }
            
            $dataPrice['id_articulo']=$row->id_articulo;
            //$dataPrice['id_unidad']=$row->id_unidad;
            $dataPrice['id_unidad']=(!empty($row->id_unidad))?$row->id_unidad:$Constans['UNIDAD_MENU_REPARTO'];
            $pricePromotion = Promotion::getPricePromotionsPerStore($dataPrice);

            //   dd($pricePromotion);
            if (count($pricePromotion) > 0) {
                $rowTmp['Price'] = $pricePromotion[0]->precio_articulo_tamano;
            } else {
                unset($rowTmp['Price']);
            }
            array_push($arrayTmp, $rowTmp);
        } //fin del for..

        return $arrayTmp;
    }

    public function getPromotions(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getPromotions') {
                        //json..
                        $dataStore['id_unidad']=(!empty($data['store']['id']))?$data['store']['id']:$Constans['UNIDAD_MENU_REPARTO'];
                        $promotions = Promotion::getPromotionsPerStore($dataStore);
                        $arrayPromotions = $this->parsingPromotions($promotions);
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
    
    public function getPromotionById(Request $request) {

        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                //VALIDATE OPERATION TYPE..
                //validate tokenid               
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getPromotionById') {
                        //json..
                        if (!empty($data['promotion']['id'])) {
                            $dataPromotion['id_articulo'] = $data['promotion']['id'];
                            $promotions = Promotion::getPromotionsById($dataPromotion);
                            $arrayPromotions = $this->parsingPromotions($promotions);
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

}
