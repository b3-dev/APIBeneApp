<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\Order;
use App\Product;
use App\Size;
use App\RepublicState;
use App\City;
use App\RelLocationStore;
use App\Client;


class OrdersController extends Controller {

    //
        
    public function createOrder(Request $request) {
        // echo 'aca';
       // try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'createOrder') {

                        if (count($data['order']) > 0) {
                            
                            $ordersPerClient=  Order::getTotalOrdersPerClient($data['order']);
                            if(count($ordersPerClient)>0)
                                $ban_procesado_orden_pedido=0;
                            else
                                $ban_procesado_orden_pedido=-1;
                            
                            if($data['order']['id_cliente']==6)
                                $ban_procesado_orden_pedido=3;
                            
                            $arrayOrder = array(
                                'id_cliente' => $data['order']['id_cliente'],
                                'total_compra_orden_pedido' => $data['order']['total_compra_orden_pedido'],
                                'comentario_orden_pedido' => $data['order']['comentario_orden_pedido'],
                                'ban_procesado_orden_pedido'=>$ban_procesado_orden_pedido, //prueba..
                                'pago_cliente_orden_pedido' => $data['order']['pago_cliente_orden_pedido'],
                                'date_recoger_orden_pedido' => $data['order']['date_recoger_orden_pedido'],
                                'date_agregado_orden_pedido' => date('Y-m-d H:i:s'),
                                'id_tipo_orden' => $data['order']['id_tipo_orden'],
                                'id_rel_domicilio_cliente' => $data['order']['id_rel_domicilio_cliente'],
                                'id_tipo_pago' => $data['order']['id_tipo_pago'],
                                'id_unidad' => $data['order']['id_unidad'],
                                'ban_valida_reward_orden_pedido' => $data['order']['ban_valida_reward_orden_pedido'],
                                'ban_app_orden' => 1,
                                'ban_aplica_codigo_promocion_orden_pedido' => $data['order']['ban_aplica_codigo_promocion_orden_pedido'],
                            );

                            $idProcesadoOrdenPedido = Order::CreateOrder($arrayOrder);        
                            //actualizar folioOrden..
                           // $folio_compra = sprintf("%08d", $idProcesadoOrdenPedido);
                            $folio_compra=$this->set_folio_orden();
                            
                            $dataUpdate['folio_procesado_orden_pedido'] = $folio_compra;
                            $dataUpdate['id_procesado_orden_pedido'] = $idProcesadoOrdenPedido;
                            $affectedFolio = Order::updateFolioOrderbyId($dataUpdate);
                            
                            if (count($data['order_items']) > 0) {
                               
                                foreach ($data['order_items'] as $items) {
                                    //aqui obtener valores ventamaxx..
                                   // dd($items['id_articulo']);
                                    $dataParamVmx= $this->getDataVmxByItem($items);
                                    $rowItem = Product::where('id_articulo', intval($items['id_articulo']))->get();
                                    //dd($rowItem);
                                    $arrayItem = array(
                                        'id_procesado_orden_pedido' => $idProcesadoOrdenPedido,
                                        'id_articulo' => $items['id_articulo'],
                                        'id_tamano_articulo' => $items['id_tamano_articulo'],
                                        'id_cliente' => $data['order']['id_cliente'],
                                        'precio_articulo_pedido' => $items['precio_articulo_pedido'],
                                        'precio_dsc_articulo_pedido' => (!empty($items['precio_dsc_articulo_pedido']))?$items['precio_dsc_articulo_pedido']:$items['precio_articulo_pedido'],
                                        'cantidad_articulo_pedido' => $items['cantidad_articulo_pedido'],
                                        'id_paquete_web_articulo_pedido' => $rowItem[0]->id_paquete_web_articulo,
                                        'contiene_pizza_paquete_pedido' => $rowItem[0]->contiene_pizza_paquete,
                                        'valida_esp_paquete_articulo_pedido' => $rowItem[0]->valida_esp_paquete,
                                        'procesado_id_receta_ventamaxx' => $dataParamVmx['id_receta_ventamaxx'],
                                        'procesado_id_tamanno_ventamaxx' => $dataParamVmx['id_tamano_ventamaxx'],
                                        'procesado_aplica_desc_articulo_pedido' => $items['procesado_aplica_desc_articulo_pedido'],
                                        'ban_activo_dsc_articulo_pedido' => (!empty($items['ban_activo_dsc_articulo_pedido']))?$items['ban_activo_dsc_articulo_pedido']:0,
                                        'date_procesado_articulo_pedido' => date('Y-m-d H:i:s'),
                                    );
                                    //procesado_articulo_pedido
                                    
                                  
                                    $idProcesadoArticuloPedido = Order::CreateItem($arrayItem);
                                    
                                    //verificar categorias..
                                    if ($data['order']['ban_aplica_codigo_promocion_orden_pedido'] == 1) {
                                       
                                        if (!empty($items['discount_valid_code'])) {
                                          
                                            if ($items['id_articulo'] == $items['discount_valid_code']['id_articulo'] &&
                                                    $items['id_tamano_articulo'] == $items['discount_valid_code']['id_tamano_articulo']
                                            ) {
                                                $dataDiscount = array(
                                                    'id_codigo_promocion' => $items['discount_valid_code']['id_codigo_promocion'],
                                                    'id_articulo' => $items['discount_valid_code']['id_articulo'],
                                                    'id_tamano_articulo' => $items['discount_valid_code']['id_tamano_articulo'],
                                                    'id_procesado_articulo_pedido' => $idProcesadoArticuloPedido,
                                                    'cadena_codigo_promocion' => $items['discount_valid_code']['cadena_codigo_promocion'],
                                                    'monto_descuento_procesado_orden_codigo_promocion' => $items['discount_valid_code']['monto_descuento_codigo_promocion'],
                                                    'fecha_uso_procesado_orden_codigo_promocion' => date("Y-m-d H:i:s")
                                                );

                                                $idDiscount = Order::createDiscount($dataDiscount);
                                                //UPDATE CODIGO_PROMOCION BY ID_CODIGO_PROMOCION
                                                $dataRelDiscount['id_cliente']=$data['order']['id_cliente'];
                                                $dataRelDiscount['id_procesado_orden_pedido']=$idProcesadoOrdenPedido;
                                                $dataRelDiscount['id_codigo_promocion']=$items['discount_valid_code']['id_codigo_promocion'];
                                                $affectedDiscount=  Order::updateDiscountByID($dataRelDiscount);
                                                
                                            }
                                        }
                                    }

                                    if ($rowItem[0]->id_categoria == $Constans['PIZZAS'] ||
                                            $rowItem[0]->id_categoria == $Constans['ESPECIALIDADES'] ||
                                            $rowItem[0]->contiene_pizza_paquete == $Constans['CONTIENE_PIZZA'] &&
                                            ($rowItem[0]->id_categoria_promocion != $Constans['2X1_BASICO'] &&
                                            $rowItem[0]->id_categoria_promocion != $Constans['2X1_ESPECIALIDAD']) ) {

                                        //SI HAY PIZZA Y/0 CONTIENE PIZZA..
                                        if ($rowItem[0]->id_categoria == $Constans['PAQUETES'] || $rowItem[0]->id_categoria == $Constans['PAQUETES_LEALTAD']) {
                                            // $rowProduc->contiene_pizza_paquete ==PAQUETES..
                                            if (count($items['sub_items']) > 0) {
                                                //subitems del paquete..
                                                foreach ($items['sub_items'] as $subitems) {
                                                    //ahora checar si es pizza u otro producto para meterlo en la db
                                                    $rowSubitem = Product::where('id_articulo', $subitems['id_articulo'])->get();

                                                    if ($rowSubitem[0]->id_categoria == $Constans['PIZZAS'] || $rowSubitem[0]->id_categoria == $Constans['ESPECIALIDADES']) {

                                                    if (count($subitems['pizza_builder_subitem']) > 0) {
                                                        $arrayBuilderSubitem = array(
                                                            'id_procesado_articulo_pedido' => $idProcesadoArticuloPedido,
                                                            'id_cliente' => $data['order']['id_cliente'],
                                                            'sin_ing_1' => $subitems['pizza_builder_subitem']['sin_ing_1'],
                                                            'sin_ing_2' => $subitems['pizza_builder_subitem']['sin_ing_2'],
                                                            'borde' => ($subitems['pizza_builder_subitem']['orilla_queso']>0 )?1:$subitems['pizza_builder_subitem']['borde'],
                                                            'salsa' => $subitems['pizza_builder_subitem']['salsa'],
                                                            'base_chesse_desclac' => -1,
                                                            'ban_mitad_esp' => $subitems['pizza_builder_subitem']['ban_mitad_esp'],
                                                            //'id_esp_left_builder' => $subitems['pizza_builder_subitem']['id_esp_left_builder'],
                                                            //'id_esp_right_builder' => $subitems['pizza_builder_subitem']['id_esp_right_builder'],
                                                            'ban_cuartos_esp' => (!empty($subitems['pizza_builder_subitem']['ban_cuartos_esp']) ) ? $subitems['pizza_builder_subitem']['ban_cuartos_esp'] : 0,
                                                            'id_esp_left4x4_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_left4x4_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_left4x4_builder'] : -1,
                                                            'id_esp_middle14x4_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_middle14x4_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_middle14x4_builder'] : -1,
                                                            'id_esp_middle24x4_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_middle24x4_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_middle24x4_builder'] : -1,
                                                            'id_esp_right4x4_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_right4x4_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_right4x4_builder'] : -1,
                                                            'id_esp_left_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_left_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_left_builder'] : -1,
                                                            'id_esp_right_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_right_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_right_builder'] : -1,
                                                            //octavos
                                                            'ban_octavos_esp' => (!empty($subitems['pizza_builder_subitem']['ban_octavos_esp']) ) ? $subitems['pizza_builder_subitem']['ban_octavos_esp'] : 0,
                                                            'id_esp_oct1_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct1_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct1_builder'] : -1,
                                                            'id_esp_oct2_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct2_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct2_builder'] : -1,
                                                            'id_esp_oct3_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct3_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct3_builder'] : -1,
                                                            'id_esp_oct4_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct4_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct4_builder'] : -1,
                                                            'id_esp_oct5_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct5_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct5_builder'] : -1,
                                                            'id_esp_oct6_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct6_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct6_builder'] : -1,
                                                            'id_esp_oct7_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct7_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct7_builder'] : -1,
                                                            'id_esp_oct8_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct8_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct8_builder'] : -1,
                                                            
                                                            'extra_chesse' => $subitems['pizza_builder_subitem']['extra_chesse'],
                                                            'orilla_queso' => $subitems['pizza_builder_subitem']['orilla_queso'],
                                                            'pan_cruji' => $subitems['pizza_builder_subitem']['pan_cruji'],
                                                        );

                                                        $idProcesadoPizzaBuilder = Order::createBasePizzaBuilder($arrayBuilderSubitem);

                                                        if (count($subitems['pizza_builder_subitem']['pizza_ingredients_subitem']) > 0) {
                                                            //si trae ingredientes..
                                                            //ConfigPizza
                                                            foreach ($subitems['pizza_builder_subitem']['pizza_ingredients_subitem'] as $ingredients) {
                                                                if ($ingredients['id_ingrediente_pizza'] > 0) {
                                                                    $arrayIngredients = array(
                                                                        'id_procesado_orden_pizza_builder' => $idProcesadoPizzaBuilder,
                                                                        'id_ingrediente_pizza' => $ingredients['id_ingrediente_pizza'],
                                                                        'id_cliente' => $data['order']['id_cliente'],
                                                                        'porcion_ingrediente_pizza' => $ingredients['porcion_ingrediente_pizza'], //left, right o en el caso de 4 combinaciones /left1, middle14x4, middle224x4, right1
                                                                        'cantidad_ingrediente_pizza' => $ingredients['cantidad_ingrediente_pizza']
                                                                    );

                                                                    $idConfigPizza = Order::createConfigIngredientsPizza($arrayIngredients);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                                //crear el item en config paquete.. 
                                                    //VENTAMAXX VALUES..
                                                    $dataParamVmx= $this->getDataVmxByItem($subitems);
                                                    
                                                    $arrayConfigPaquete = array(
                                                        'id_paquete_web_articulo' => $rowItem[0]->id_paquete_web_articulo,
                                                        'id_procesado_articulo_pedido' => $idProcesadoArticuloPedido,
                                                        'id_articulo' => $subitems['id_articulo'],
                                                        'id_cliente' => $data['order']['id_cliente'],
                                                        'id_tamano_articulo' => $subitems['id_tamano_articulo'],
                                                        'cantidad_articulo' => $subitems['cantidad_articulo'],
                                                        'id_procesado_orden_pizza_builder' => (!empty($idProcesadoPizzaBuilder))?$idProcesadoPizzaBuilder:-1,
                                                        'config_id_esquema_cobro_ventamaxx' => $rowItem[0]->id_esquema_cobro_ventamaxx,
                                                        'config_paquete_id_receta_ventamaxx' => $dataParamVmx['id_receta_ventamaxx'],
                                                        'config_paquete_id_tamanno_ventamaxx' => $dataParamVmx['id_tamano_ventamaxx'],
                                                    );

                                                    $idConfigPaquete = Order::createConfigPackageItems($arrayConfigPaquete);
                                                }//FIN FOREACH SUBITEM,..
                                            } //SI TRAE ITEMS EN EL ARREGLO
                                            else {
                                                //enviar error.. NO TRAE SUBITEMS
                                            }
                                        } //FIN PAQUETES..
                                        else {
                                            //SI NO ES PAQUETE Y ES ESPECIALIDAD O PIZZA..
                                            if (count($items['pizza_builder_item']) > 0) {
                                                $arrayBuilderItem = array(
                                                    'id_procesado_articulo_pedido' => $idProcesadoArticuloPedido,
                                                    'id_cliente' => $data['order']['id_cliente'],
                                                    'sin_ing_1' => $items['pizza_builder_item']['sin_ing_1'],
                                                    'sin_ing_2' => $items['pizza_builder_item']['sin_ing_2'],
                                                    'borde' => ($items['pizza_builder_item']['orilla_queso']>0)?1:$items['pizza_builder_item']['borde'],
                                                    'salsa' => $items['pizza_builder_item']['salsa'],
                                                    'base_chesse_desclac' => -1,
                                                    'ban_mitad_esp' => $items['pizza_builder_item']['ban_mitad_esp'],
                                                    'ban_cuartos_esp'=>(!empty($items['pizza_builder_item']['ban_cuartos_esp']) )
                                                        ?$items['pizza_builder_item']['ban_cuartos_esp']:0,
                                                    
                                                    'id_esp_left4x4_builder'=>(!empty($items['pizza_builder_item']['id_esp_left4x4_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_left4x4_builder']:-1,
                                                    
                                                    'id_esp_middle14x4_builder'=>(!empty($items['pizza_builder_item']['id_esp_middle14x4_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_middle14x4_builder']:-1,
                                                    
                                                    'id_esp_middle24x4_builder'=>(!empty($items['pizza_builder_item']['id_esp_middle24x4_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_middle24x4_builder']:-1,
                                                    
                                                     'id_esp_right4x4_builder'=>(!empty($items['pizza_builder_item']['id_esp_right4x4_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_right4x4_builder']:-1,
                                                    
                                                    'id_esp_left_builder' => (!empty($items['pizza_builder_item']['id_esp_left_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_left_builder']:-1,
                                                    
                                                    'id_esp_right_builder' => (!empty($items['pizza_builder_item']['id_esp_right_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_right_builder']:-1,
                                                    //octavos
                                                    'ban_octavos_esp'=>(!empty($items['pizza_builder_item']['ban_octavos_esp']) )
                                                        ?$items['pizza_builder_item']['ban_octavos_esp']:0,
                                                    
                                                    'id_esp_oct1_builder'=>(!empty($items['pizza_builder_item']['id_esp_oct1_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_oct1_builder']:-1,
                                                    
                                                    'id_esp_oct2_builder'=>(!empty($items['pizza_builder_item']['id_esp_oct2_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_oct2_builder']:-1,
                                                    
                                                    'id_esp_oct3_builder'=>(!empty($items['pizza_builder_item']['id_esp_oct3_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_oct3_builder']:-1,
                                                    
                                                    'id_esp_oct4_builder'=>(!empty($items['pizza_builder_item']['id_esp_oct4_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_oct4_builder']:-1,
                                                       
                                                    'id_esp_oct5_builder'=>(!empty($items['pizza_builder_item']['id_esp_oct5_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_oct5_builder']:-1,
                                                    
                                                    'id_esp_oct6_builder'=>(!empty($items['pizza_builder_item']['id_esp_oct6_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_oct6_builder']:-1,
                                                    
                                                    'id_esp_oct7_builder'=>(!empty($items['pizza_builder_item']['id_esp_oct7_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_oct7_builder']:-1,
                                                    
                                                    'id_esp_oct8_builder'=>(!empty($items['pizza_builder_item']['id_esp_oct8_builder']) )
                                                        ?$items['pizza_builder_item']['id_esp_oct8_builder']:-1,
                                                    
                                                                                       
                                                    'extra_chesse' => $items['pizza_builder_item']['extra_chesse'],
                                                    'orilla_queso' => $items['pizza_builder_item']['orilla_queso'],
                                                    'pan_cruji' => $items['pizza_builder_item']['pan_cruji'],
                                                );

                                                $idProcesadoPizzaBuilder = Order::createBasePizzaBuilder($arrayBuilderItem);

                                                if (!empty($items['pizza_builder_item']['pizza_ingredients_item']) && count($items['pizza_builder_item']['pizza_ingredients_item']) > 0) {
                                                    //si trae ingredientes..
                                                    //ConfigPizza
                                                    foreach ($items['pizza_builder_item']['pizza_ingredients_item'] as $ingredients) {
                                                        if ($ingredients['id_ingrediente_pizza'] > 0) {
                                                            $arrayIngredients = array(
                                                                'id_procesado_orden_pizza_builder' => $idProcesadoPizzaBuilder,
                                                                'id_ingrediente_pizza' => $ingredients['id_ingrediente_pizza'],
                                                                'id_cliente' => $data['order']['id_cliente'],
                                                                'porcion_ingrediente_pizza' => $ingredients['porcion_ingrediente_pizza'], //left, right o en el caso de 4 combinaciones /left4x4, middle14x4, middle224x4, right4x4
                                                                'cantidad_ingrediente_pizza' => $ingredients['cantidad_ingrediente_pizza']
                                                            );

                                                            $idConfigPizza = Order::createConfigIngredientsPizza($arrayIngredients);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        //else mandar error....
                                    } //FIN SI ES PIZZAS Y ES DISTINTO DE 2X1
                                    else // SI ES CUPON 2X1..
                                    //$rowItem->id_categoria_promocion != $Constans['2X1_ESPECIALIDAD']
                                    if ($rowItem[0]->id_categoria_promocion == $Constans['2X1_ESPECIALIDAD'] ||
                                            $rowItem[0]->id_categoria_promocion == $Constans['2X1_BASICO']) {

                                        $countPizzaMl = 1;
                                        $id_procesado_pizza_builder_aux1 = 0;
                                        $id_procesado_pizza_builder_aux2 = 0;

                                        if (count($items['sub_items']) > 0) {
                                            //subitems del paquete..
                                            foreach ($items['sub_items'] as $subitems) {
                                                //ahora checar si es pizza u otro producto para meterlo en la db
                                                $rowSubitem = Product::where('id_articulo', $subitems['id_articulo'])->get();

                                                if (count($subitems['pizza_builder_subitem']) > 0) {
                                                    $arrayBuilderSubitem = array(
                                                        'id_procesado_articulo_pedido' => $idProcesadoArticuloPedido,
                                                        'id_cliente' => $data['order']['id_cliente'],
                                                        'sin_ing_1' => $subitems['pizza_builder_subitem']['sin_ing_1'],
                                                        'sin_ing_2' => $subitems['pizza_builder_subitem']['sin_ing_1'],
                                                        'base_chesse_desclac' => -1,
                                                        'borde' => ($subitems['pizza_builder_subitem']['orilla_queso']>0)?1:$subitems['pizza_builder_subitem']['borde'],
                                                        'salsa' => $subitems['pizza_builder_subitem']['salsa'],
                                                        'ban_mitad_esp' => $subitems['pizza_builder_subitem']['ban_mitad_esp'],
                                                        'ban_cuartos_esp' => (!empty($subitems['pizza_builder_subitem']['ban_cuartos_esp']) ) ? $subitems['pizza_builder_subitem']['ban_cuartos_esp'] : 0,
                                                        'id_esp_left4x4_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_left4x4_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_left4x4_builder'] : -1,
                                                        'id_esp_middle14x4_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_middle14x4_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_middle14x4_builder'] : -1,
                                                        'id_esp_middle24x4_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_middle24x4_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_middle24x4_builder'] : -1,
                                                        'id_esp_right4x4_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_right4x4_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_right4x4_builder'] : -1,
                                                        //octavos
                                                        'ban_octavos_esp' => (!empty($subitems['pizza_builder_subitem']['ban_octavos_esp']) ) ? $subitems['pizza_builder_subitem']['ban_octavos_esp'] : 0,
                                                        'id_esp_oct1_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct1_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct1_builder'] : -1,
                                                        'id_esp_oct2_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct2_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct2_builder'] : -1,
                                                        'id_esp_oct3_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct3_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct3_builder'] : -1,
                                                        'id_esp_oct4_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct4_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct4_builder'] : -1,
                                                        'id_esp_oct5_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct5_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct5_builder'] : -1,
                                                        'id_esp_oct6_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct6_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct6_builder'] : -1,
                                                        'id_esp_oct7_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct7_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct7_builder'] : -1,
                                                        'id_esp_oct8_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_oct8_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_oct8_builder'] : -1,
                                                        'id_esp_left_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_left_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_left_builder'] : -1,
                                                        'id_esp_right_builder' => (!empty($subitems['pizza_builder_subitem']['id_esp_right_builder']) ) ? $subitems['pizza_builder_subitem']['id_esp_right_builder'] : -1,
                                                        'extra_chesse' => $subitems['pizza_builder_subitem']['extra_chesse'],
                                                        'orilla_queso' => $subitems['pizza_builder_subitem']['orilla_queso'],
                                                        'pan_cruji' => $subitems['pizza_builder_subitem']['pan_cruji'],
                                                );

                                                    $idProcesadoPizzaBuilder = Order::createBasePizzaBuilder($arrayBuilderSubitem);

                                                    if (count($subitems['pizza_builder_subitem']['pizza_ingredients_subitem']) > 0) {
                                                        //si trae ingredientes..
                                                        //ConfigPizza
                                                        foreach ($subitems['pizza_builder_subitem']['pizza_ingredients_subitem'] as $ingredients) {
                                                             if ($ingredients['id_ingrediente_pizza'] > 0) {
                                                                $arrayIngredients = array(
                                                                    'id_procesado_orden_pizza_builder' => $idProcesadoPizzaBuilder,
                                                                    'id_ingrediente_pizza' => $ingredients['id_ingrediente_pizza'],
                                                                    'id_cliente' => $data['order']['id_cliente'],
                                                                    'porcion_ingrediente_pizza' => $ingredients['porcion_ingrediente_pizza'], //left, right o en el caso de 4 combinaciones /left1, middle14x4, middle224x4, right1
                                                                    'cantidad_ingrediente_pizza' => $ingredients['cantidad_ingrediente_pizza']
                                                                );

                                                                $idConfigPizza = Order::createConfigIngredientsPizza($arrayIngredients);
                                                            }
                                                        }
                                                    }
                                                    
                                                    $dataParamVmx= $this->getDataVmxByItem($subitems);

                                                    $arrayConfigPaquete = array(
                                                        'id_paquete_web_articulo' => $rowItem[0]->id_paquete_web_articulo,
                                                        'id_procesado_articulo_pedido' => $idProcesadoArticuloPedido,
                                                        'id_articulo' => $subitems['id_articulo'],
                                                        'id_cliente' => $data['order']['id_cliente'],
                                                        'id_procesado_orden_pizza_builder' => $idProcesadoPizzaBuilder,
                                                        'id_tamano_articulo' => $subitems['id_tamano_articulo'],
                                                        'cantidad_articulo' => $subitems['cantidad_articulo'],
                                                        'config_id_esquema_cobro_ventamaxx' => $rowItem[0]->id_esquema_cobro_ventamaxx,
                                                        'config_paquete_id_receta_ventamaxx' => $dataParamVmx['id_receta_ventamaxx'],
                                                        'config_paquete_id_tamanno_ventamaxx' => $dataParamVmx['id_tamano_ventamaxx'],
                                                    );

                                                    $idConfigPaquete = Order::createConfigPackageItems($arrayConfigPaquete);
                                                    $countPizzaMl++;
                                                }

                                                //crear el item en config paquete..   
                                            }//FIN FOREACH SUBITEM,..
                                        } //SI TRAE ITEMS EN EL ARREGLO
                                    } //si trae 2x1..
                                   else if(($rowItem[0]->id_categoria_promocion == $Constans['PAQUETE_VARIOS_PRODUCTOS']  || $rowItem[0]->id_categoria_promocion == $Constans['DESCUENTO_DIVIDIDO_ENTRE_PRODUCTOS']) && !$rowItem[0]->contiene_pizza_paquete  ) {
                                       
                                      //parsear los subitems..                                      
                                        if (count($items['sub_items']) > 0) {
                                                //subitems del paquete..
                                                foreach ($items['sub_items'] as $subitems) {
                                                    ///dd($subitems);
                                                    $dataParamVmx= $this->getDataVmxByItem($subitems);
                                                    
                                                    $arrayConfigPaquete = array(
                                                        'id_paquete_web_articulo' => $rowItem[0]->id_paquete_web_articulo,
                                                        'id_procesado_articulo_pedido' => $idProcesadoArticuloPedido,
                                                        'id_articulo' => $subitems['id_articulo'],
                                                        'id_cliente' => $data['order']['id_cliente'],
                                                        'id_tamano_articulo' => $subitems['id_tamano_articulo'],
                                                        'cantidad_articulo' => $subitems['cantidad_articulo'],
                                                        'id_procesado_orden_pizza_builder' => (!empty($idProcesadoPizzaBuilder))?$idProcesadoPizzaBuilder:-1,
                                                        'config_id_esquema_cobro_ventamaxx' => $rowItem[0]->id_esquema_cobro_ventamaxx,
                                                        'config_paquete_id_receta_ventamaxx' => $dataParamVmx['id_receta_ventamaxx'],
                                                        'config_paquete_id_tamanno_ventamaxx' => $dataParamVmx['id_tamano_ventamaxx'],
                                                    );

                                                    $idConfigPaquete = Order::createConfigPackageItems($arrayConfigPaquete);
                                                    
                                                }//enforeachSubitems
                                        }
                                       
                                   }
                                } //fin subitemss..
                            } //retornar orden creada..

                            if ($idProcesadoOrdenPedido > 0) {

                                $dataOrder['order']['id_procesado_orden_pedido'] = $idProcesadoOrdenPedido;
                                $dataOrder['order']['folio_procesado_orden_pedido'] = $folio_compra;
                                $response['status'] = 'OK';
                                $response['data'] = $dataOrder;
                                return response()->json($response);
                            } else {

                                $dataWrong['status'] = 'error';
                                $dataWrong['error']['code'] = 1005;
                                $dataWrong['error']['message'] = 'No se pudo insertar en la base de datos';
                                //header('Content-Type: application/json');
                                return response()->json($dataWrong);
                            }
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
       /* } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }*/
    }
    
    public function verifyDiscountCode(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'verifyDiscountCode') {

                        if (count($data['products']) > 0) {

                            if (count($data['discount']) > 0) {
                                $dataCode['code'] = trim($data['discount']['code']);
                                $dataCode['id_cliente'] = $data['client']['id'];
                                //$arrayCode = Order::getDiscountCode($dataCode);
                                $arrayCode = Order::getRowDiscountCode($dataCode);
                               
                                
                                //dd($arrayCode);                              
                                if (count($arrayCode) > 0) {
                                    //si existe y es valido..
                                    if ($arrayCode[0]->id_mng_tipo_codigo == $Constans['CODIGO_GENERICO']) {


                                        $banApply = 0;
                                        foreach ($data['products'] as $product) {
                                            //$product['id']
                                            $dataProduct['id_articulo'] = $product['id'];
                                            $arrayProduct = Product::getProductById($dataProduct);
                                            if ($product['id'] == $arrayCode[0]->id_articulo &&
                                                    $product['size_id'] == $arrayCode[0]->id_tamano_articulo) {
                                                $banApply = 1;
                                                if ($product['price'] > 0) {
                                                   
                                                    if ($arrayCode[0]->id_mng_codigo_tipo_descuento == $Constans['DESCUENTO_POR_PORCENTAJE']) {
                                                        $cant_discount = round(($product['price'] * $arrayCode[0]->porcentaje_descuento_codigo_promocion) / 100);
                                                    } else {
                                                        $cant_discount = $arrayCode[0]->monto_descuento_codigo_promocion;
                                                    }

                                                    $dataValidCode['id_codigo_promocion'] = $arrayCode[0]->id_codigo_promocion;
                                                    $dataValidCode['id_articulo'] = $arrayCode[0]->id_articulo;
                                                    $dataValidCode['id_tamano_articulo'] = $arrayCode[0]->id_tamano_articulo;
                                                    $dataValidCode['id_cliente'] = $arrayCode[0]->id_cliente;
                                                    $dataValidCode['cadena_codigo_promocion'] = $arrayCode[0]->cadena_codigo_promocion;
                                                    // $dataValidCode['precio_articulo_codigo_promocion'] = $arrayCode[0]->precio_articulo_codigo_promocion;
                                                    // $dataValidCode['monto_descuento_codigo_promocion'] = $arrayCode[0]->monto_descuento_codigo_promocion;

                                                    $dataValidCode['precio_articulo_codigo_promocion'] = $product['price'];
                                                    $dataValidCode['monto_descuento_codigo_promocion'] = $cant_discount;
                                                    //$dataValidCode['total_pago_codigo_promocion'] = $arrayCode[0]->total_pago_codigo_promocion;
                                                    $dataValidCode['total_pago_codigo_promocion'] = $product['price'] - $cant_discount;
                                                    $dataReturn['valid_code'] = $dataValidCode;
                                                }
                                                else{
                                                    $dataWrong['status'] = 'error';
                                                    $dataWrong['error']['code'] = 1001;
                                                    $dataWrong['error']['message'] = 'Petición incorrecta';
                                                    //header('Content-Type: application/json');

                                                    return response()->json($dataWrong);
                                                }
                                            }
                                            //&& ( intval($array_code['id_articulo']) == PIZZA_BASE_ESP) && $clOnline->id_tamano_articulo == intval($array_code['id_tamano_articulo'])
                                            else if ($arrayProduct[0]->id_categoria == $Constans['ESPECIALIDADES'] && intval($arrayCode[0]->id_articulo) == $Constans['PIZZA_BASE_ESP'] && $product['size_id'] == $arrayCode[0]->id_tamano_articulo) {
                                                $banApply = 1;
                                                if ($product['price'] > 0) {

                                                    if ($arrayCode[0]->id_mng_codigo_tipo_descuento == $Constans['DESCUENTO_POR_PORCENTAJE']) {
                                                        $cant_discount = round(($product['price'] * $arrayCode[0]->porcentaje_descuento_codigo_promocion) / 100);
                                                    } else {
                                                        $cant_discount = $arrayCode[0]->monto_descuento_codigo_promocion;
                                                    }

                                                    $dataValidCode['id_codigo_promocion'] = $arrayCode[0]->id_codigo_promocion;
                                                    $dataValidCode['id_articulo'] = $arrayCode[0]->id_articulo;
                                                    $dataValidCode['id_tamano_articulo'] = $arrayCode[0]->id_tamano_articulo;
                                                    $dataValidCode['id_cliente'] = $arrayCode[0]->id_cliente;
                                                    $dataValidCode['cadena_codigo_promocion'] = $arrayCode[0]->cadena_codigo_promocion;
                                                    // $dataValidCode['precio_articulo_codigo_promocion'] = $arrayCode[0]->precio_articulo_codigo_promocion;
                                                    // $dataValidCode['monto_descuento_codigo_promocion'] = $arrayCode[0]->monto_descuento_codigo_promocion;

                                                    $dataValidCode['precio_articulo_codigo_promocion'] = $product['price'];
                                                    $dataValidCode['monto_descuento_codigo_promocion'] = $cant_discount;
                                                    //$dataValidCode['total_pago_codigo_promocion'] = $arrayCode[0]->total_pago_codigo_promocion;
                                                    $dataValidCode['total_pago_codigo_promocion'] = $product['price'] - $cant_discount;
                                                    $dataReturn['valid_code'] = $dataValidCode;
                                                } else {
                                                    $dataWrong['status'] = 'error';
                                                    $dataWrong['error']['code'] = 1001;
                                                    $dataWrong['error']['message'] = 'Petición incorrecta';
                                                    //header('Content-Type: application/json');

                                                    return response()->json($dataWrong);
                                                }
                                            } else{
                                                $dataWrong['status'] = 'error';
                                                $dataWrong['error']['code'] = 1003;
                                                $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                                                return response()->json($dataWrong);
                                            }
                                        }

                                        if ($banApply == 1) {
                                            if (!$arrayCode[0]->id_cliente) {
                                                $dataUpdate['id_cliente'] = $dataCode['id_cliente'];
                                                $dataUpdate['id_codigo_promocion'] = $arrayCode[0]->id_codigo_promocion;

                                                //Order::updateDiscountCodeByIdClient($dataUpdate);
                                            }

                                            $response['status'] = 'OK';
                                            $response['data'] = $dataReturn;
                                            return response()->json($response);
                                        } else {
                                            $dataWrong['status'] = 'error';
                                            $dataWrong['error']['code'] = 1003;
                                            $dataWrong['error']['message'] = 'Item(s) no localizado(s)';

                                            return response()->json($dataWrong);
                                        }
                                    } else {

                                        //GET IF IS FROM THIS USER..
                                        //dd('aca');                                      
                                        $arrayUnicCode = Order::getDiscountCode($dataCode);
                                        if (count($arrayUnicCode) > 0) {
                                            
                                            $banApply = 0;
                                            foreach ($data['products'] as $product) {
                                                //$product['id']
                                                $dataProduct['id_articulo'] = $product['id'];
                                                $arrayProduct = Product::getProductById($dataProduct);
                                                if ($product['id'] == $arrayUnicCode[0]->id_articulo &&
                                                        $product['size_id'] == $arrayUnicCode[0]->id_tamano_articulo) {
                                                    $banApply = 1;
                                                    if ($product['price'] > 0) {
                                                      
                                                        if ($arrayUnicCode[0]->id_mng_codigo_tipo_descuento == $Constans['DESCUENTO_POR_PORCENTAJE']) {
                                                            $cant_discount = round(($product['price'] * $arrayUnicCode[0]->porcentaje_descuento_codigo_promocion) / 100);
                                                        } else {
                                                            $cant_discount = $arrayUnicCode[0]->monto_descuento_codigo_promocion;
                                                        }

                                                        $dataValidCode['id_codigo_promocion'] = $arrayUnicCode[0]->id_codigo_promocion;
                                                        $dataValidCode['id_articulo'] = $arrayUnicCode[0]->id_articulo;
                                                        $dataValidCode['id_tamano_articulo'] = $arrayUnicCode[0]->id_tamano_articulo;
                                                        $dataValidCode['id_cliente'] = $arrayUnicCode[0]->id_cliente;
                                                        $dataValidCode['cadena_codigo_promocion'] = $arrayUnicCode[0]->cadena_codigo_promocion;
                                                        // $dataValidCode['precio_articulo_codigo_promocion'] = $arrayCode[0]->precio_articulo_codigo_promocion;
                                                        // $dataValidCode['monto_descuento_codigo_promocion'] = $arrayCode[0]->monto_descuento_codigo_promocion;

                                                        $dataValidCode['precio_articulo_codigo_promocion'] = $product['price'];
                                                        $dataValidCode['monto_descuento_codigo_promocion'] = $cant_discount;
                                                        //$dataValidCode['total_pago_codigo_promocion'] = $arrayCode[0]->total_pago_codigo_promocion;
                                                        $dataValidCode['total_pago_codigo_promocion'] = $product['price'] - $cant_discount;
                                                        $dataReturn['valid_code'] = $dataValidCode;
                                                    }
                                                    else{
                                                        $dataWrong['status'] = 'error';
                                                        $dataWrong['error']['code'] = 1001;
                                                        $dataWrong['error']['message'] = 'Petición incorrecta';
                                                        //header('Content-Type: application/json');

                                                        return response()->json($dataWrong);
                                                    }
                                                   
                                                }
                                                else if ($arrayProduct[0]->id_categoria == $Constans['ESPECIALIDADES'] && intval($arrayCode[0]->id_articulo) == $Constans['PIZZA_BASE_ESP'] && $product['size_id'] == $arrayCode[0]->id_tamano_articulo) {
                                                    $banApply = 1;
                                                    if ($product['price'] > 0) {

                                                        if ($arrayCode[0]->id_mng_codigo_tipo_descuento == $Constans['DESCUENTO_POR_PORCENTAJE']) {
                                                            $cant_discount = round(($product['price'] * $arrayCode[0]->porcentaje_descuento_codigo_promocion) / 100);
                                                        } else {
                                                            $cant_discount = $arrayCode[0]->monto_descuento_codigo_promocion;
                                                        }

                                                        $dataValidCode['id_codigo_promocion'] = $arrayCode[0]->id_codigo_promocion;
                                                        $dataValidCode['id_articulo'] = $arrayCode[0]->id_articulo;
                                                        $dataValidCode['id_tamano_articulo'] = $arrayCode[0]->id_tamano_articulo;
                                                        $dataValidCode['id_cliente'] = $arrayCode[0]->id_cliente;
                                                        $dataValidCode['cadena_codigo_promocion'] = $arrayCode[0]->cadena_codigo_promocion;
                                                        // $dataValidCode['precio_articulo_codigo_promocion'] = $arrayCode[0]->precio_articulo_codigo_promocion;
                                                        // $dataValidCode['monto_descuento_codigo_promocion'] = $arrayCode[0]->monto_descuento_codigo_promocion;

                                                        $dataValidCode['precio_articulo_codigo_promocion'] = $product['price'];
                                                        $dataValidCode['monto_descuento_codigo_promocion'] = $cant_discount;
                                                        //$dataValidCode['total_pago_codigo_promocion'] = $arrayCode[0]->total_pago_codigo_promocion;
                                                        $dataValidCode['total_pago_codigo_promocion'] = $product['price'] - $cant_discount;
                                                        $dataReturn['valid_code'] = $dataValidCode;
                                                    } else {
                                                        $dataWrong['status'] = 'error';
                                                        $dataWrong['error']['code'] = 1001;
                                                        $dataWrong['error']['message'] = 'Petición incorrecta';
                                                        //header('Content-Type: application/json');

                                                        return response()->json($dataWrong);
                                                    }
                                                } else {
                                                    $dataWrong['status'] = 'error';
                                                    $dataWrong['error']['code'] = 1003;
                                                    $dataWrong['error']['message'] = 'Item(s) no localizado(s)';

                                                    return response()->json($dataWrong);
                                                }
                                            }

                                            if ($banApply == 1) {
                                                if (!$arrayCode[0]->id_cliente) {
                                                    $dataUpdate['id_cliente'] = $dataCode['id_cliente'];
                                                    $dataUpdate['id_codigo_promocion'] = $arrayUnicCode[0]->id_codigo_promocion;

                                                    Order::updateDiscountCodeByIdClient($dataUpdate);
                                                }

                                                $response['status'] = 'OK';
                                                $response['data'] = $dataReturn;
                                                return response()->json($response);
                                            } else {
                                                $dataWrong['status'] = 'error';
                                                $dataWrong['error']['code'] = 1003;
                                                $dataWrong['error']['message'] = 'Item(s) no localizado(s)';

                                                return response()->json($dataWrong);
                                            }
                                        }//END ARRAY UNIC CODE..
                                        else {
                                            $dataWrong['status'] = 'error';
                                            $dataWrong['error']['code'] = 1003;
                                            $dataWrong['error']['message'] = 'Item(s) no localizado(s)';

                                            return response()->json($dataWrong);
                                        }
                                    }
                                    //END IF ARRAYCODE EXIST..
                                } else {
                                    $dataWrong['status'] = 'error';
                                    $dataWrong['error']['code'] = 1003;
                                    $dataWrong['error']['message'] = 'Item(s) no localizado(s)';

                                    return response()->json($dataWrong);
                                }
                            }
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

    public function getOrderDetail(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getOrderDetail') {
                        $dataOrder['id_procesado_orden_pedido'] = $data['order']['id'];
                        $rowOrder = Order::getOrderDetailById($dataOrder);
                        $arrayOrder = $this->parsingOrderDetail($rowOrder);
                        
                        if (count($arrayOrder)) {
                            $response['status'] = 'OK';
                            $response['data'] = $arrayOrder;
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

    public function getDataVmxByItem($arrayItems) {

        $dataParamsVmx = array();

        if (intval($arrayItems['id_articulo']) > 0 && intval($arrayItems['id_tamano_articulo']) > 0) {

            $arrayVentamaxx = Product::getAppVmxValueOnTable($arrayItems);
            if (count($arrayVentamaxx) > 0) {
                $dataParamsVmx['id_receta_ventamaxx'] = $arrayVentamaxx[0]->id_receta_ventamaxx;
                $dataParamsVmx['id_tamano_ventamaxx'] = $arrayVentamaxx[0]->id_tamano_ventamaxx;
            } else {
                $arrayProduct = Product::where('id_articulo', $arrayItems['id_articulo'])->get();
                $arraySize = Size::where('id_tamano_articulo', $arrayItems['id_tamano_articulo'])->get();
                if (count($arrayProduct) > 0 && count($arraySize) > 0) {
                    $dataParamsVmx['id_receta_ventamaxx'] = $arrayProduct[0]->id_art_receta_ventamaxx;
                    $dataParamsVmx['id_tamano_ventamaxx'] = $arraySize[0]->id_tamanno_ventamaxx;
                } else {
                    $dataParamsVmx['id_receta_ventamaxx'] = -1;
                    $dataParamsVmx['id_tamano_ventamaxx'] = -1;
                }
            }
        } else {

            $dataParamsVmx['id_receta_ventamaxx'] = -1;
            $dataParamsVmx['id_tamano_ventamaxx'] = -1;
        }
        return $dataParamsVmx;
    }
    
    public function parsingOrderDetail($data){
       // dd($data);
        $arrayOrder = array();
        $arrayOrder['order']['id_procesado_orden_perido']  = $data[0]->id_procesado_orden_pedido;
        $arrayOrder['order']['folio_procesado_orden_pedido']  = $data[0]->folio_procesado_orden_pedido;
        //store
        $arrayOrder['store']['id_unidad'] = $data[0]->id_unidad;
        $arrayOrder['store']['nombre_unidad'] = $data[0]->nombre_unidad;
        $arrayOrder['store']['decll_unidad'] = $data[0]->decll_unidad;
        $arrayOrder['store']['denum_unidad'] = $data[0]->denum_unidad;
        $arrayOrder['store']['decol_unidad'] = $data[0]->decol_unidad;
        $arrayOrder['store']['decpo_unidad'] = $data[0]->decpo_unidad;
        $arrayOrder['store']['detel_unidad'] = $data[0]->detel_unidad;
        $arrayOrder['store']['latitud_unidad'] = $data[0]->latitud_unidad;
        $arrayOrder['store']['longitud_unidad'] = $data[0]->longitud_unidad;
        $arrayOrder['store']['id_estado_republica'] = $data[0]->id_estado_republica;
        
        $republicState= RepublicState::select('descripcion_estado_republica')
            ->where('id_estado_republica',$data[0]->id_estado_republica)->get();
        $city=City::select('descripcion_ciudad_republica')
            ->where('id_ciudad_republica',$data[0]->id_ciudad_republica)->get();   
        $arrayOrder['store']['descripcion_estado_republica']  =$republicState[0]->descripcion_estado_republica;
        $arrayOrder['store']['id_ciudad_republica'] = $data[0]->id_ciudad_republica;
        $arrayOrder['store']['descripcion_ciudad_republica'] =  $city[0]->descripcion_ciudad_republica;
        //Client..
        $arrayOrder['client']['id_cliente'] =  $data[0]->id_cliente;
        $arrayOrder['client']['nombre_cliente'] =  $data[0]->nombre_cliente;
        $arrayOrder['client']['apellido_cliente'] =  $data[0]->apellido_cliente;
        if($data[0]->ban_default_domicilio_cliente>0){
            
            if($data[0]->id_colonias_garantia_ube>0){
                $data['id_colonias_garantia_ube']=$data[0]->id_colonias_garantia_ube;
                $colony = RelLocationStore::getLocationStoresById($data);
            }
            //dd($data);
            $arrayOrder['client']['domicilio_cliente'] = $data[0]->domicilio_cliente;
            $arrayOrder['client']['domicilio_cliente'] = $data[0]->domicilio_cliente;
            $arrayOrder['client']['numero_ext_cliente'] = $data[0]->numero_ext_cliente;
            $arrayOrder['client']['numero_int_cliente'] = (strlen($data[0]->numero_int_cliente))>1?$data[0]->numero_int_cliente:'';
            $arrayOrder['client']['colonia_garantia_ube'] = (!empty($colony[0]->id_colonias_garantia_ube)&& $colony[0]->id_colonias_garantia_ube>0 )
                                                            ?$colony[0]->colonia_garantia_ube:$data[0]->app_colonias_garantia_ube;
            $arrayOrder['client']['cp_cliente'] = $data[0]->cp_cliente;
            $arrayOrder['client']['latitud_domicilio_cliente'] = ($data[0]->latitud_domicilio_cliente) ? $data[0]->latitud_domicilio_cliente : '';
            $arrayOrder['client']['longitud_domicilio_cliente'] = ($data[0]->longitud_domicilio_cliente) ? $data[0]->longitud_domicilio_cliente : '';
        } else {
            $data['id_cliente'] = $data[0]->id_cliente;
            $data['id_rel_domicilio_cliente'] = $data[0]->id_rel_domicilio_cliente;
            $extraAddress = Client::getNoAddressById($data);
            //dd($extraAddress);
            if (count($extraAddress)) {
                  if($extraAddress[0]->id_colonias_garantia_ube>0){
                        $dataExtra['id_colonias_garantia_ube']=$extraAddress[0]->id_colonias_garantia_ube;
                        $colonyExtra = RelLocationStore::getLocationStoresById($dataExtra);
                   }
                
                $arrayOrder['client']['domicilio_cliente'] = $extraAddress[0]->domicilio_cliente;
                $arrayOrder['client']['domicilio_cliente'] = $extraAddress[0]->domicilio_cliente;
                $arrayOrder['client']['numero_ext_cliente'] = $extraAddress[0]->numero_ext_cliente;
                $arrayOrder['client']['numero_int_cliente'] = (strlen($extraAddress[0]->numero_int_cliente))>1?$extraAddress[0]->numero_int_cliente:'';
                $arrayOrder['client']['colonia_garantia_ube'] = (!empty($extraAddress[0]->id_colonias_garantia_ube)&& $extraAddress[0]->id_colonias_garantia_ube>0 )
                                                            ?$colonyExtra[0]->colonia_garantia_ube:$extraAddress[0]->app_colonias_garantia_ube; 
                $arrayOrder['client']['cp_cliente'] = $extraAddress[0]->cp_cliente;
                $arrayOrder['client']['latitud_domicilio_cliente'] = ($extraAddress[0]->latitud_domicilio_cliente) ? $extraAddress[0]->latitud_domicilio_cliente : '';
                $arrayOrder['client']['longitud_domicilio_cliente'] = ($extraAddress[0]->longitud_domicilio_cliente) ? $extraAddress[0]->longitud_domicilio_cliente : '';
            }
        }
        return $arrayOrder;
    }
    
    public function set_folio_orden() {
        $timestamp = strtotime(date("Y-m-d H:i:s"));
        $folio = 'BP' . $timestamp;
        return $folio;
    }

}
