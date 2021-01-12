<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\Order;
use App\Product;
use App\Size;
use App\Processor;
use App\Store;
use App\Client;
use App\Ingredient;
use App\Category;


class ProcessorController extends Controller {
    //
     
    
    public function parsingDataRecordClient($arrayClient) {
        $arrayTmpClient = array();
        $arrayTmpClient['nombre_cliente'] = $arrayClient[0]->nombre_cliente;
        $arrayTmpClient['apellido_cliente'] = $arrayClient[0]->apellido_cliente;
        $arrayTmpClient['email_cliente'] = $arrayClient[0]->email_cliente;
        return $arrayTmpClient;
    }

    public function OrdersProcessor($idUbe) {
        $Constans = Config::get('constants.options');
   
        if (!empty($idUbe) && $idUbe > 0) {
            $data['id_unidad'] = $idUbe;
            $json_arr=array();
            //$id_unidad = 82; //TODO LO QUE SAQUE DE LA TIENDA 10, LO DIRIGE A LA 24
            $arrayNotProcessed = Processor::getNotProcessedOrders($data); //ban_app=1;
            // dd($arrayNotProcessed);          
            if (count($arrayNotProcessed) > 0) {

                $sub_index_cont_arr = 0;
                $cont_orden = 0;
                foreach ($arrayNotProcessed as $rowNotProcessed) {

                    if (Store::getStatusTaxStore($data) > 0):
                        //calculos para el cobro del iva..
                        $cant_sin_iva = ($rowNotProcessed->total_compra_orden_pedido / 1.16);
                        $cant_iva_round = ($cant_sin_iva * 100) / 100;
                        //desgloce..
                        $iva_float = $cant_iva_round * 0.16;
                        $iva = ($iva_float * 100) / 100;
                        $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];
                    else:
                        $iva = 0;
                        $impporc_producto = 0;
                    endif;

                    if ($rowNotProcessed->id_tipo_orden == $Constans['ID_MOSTRADOR']):

                        $ventamaxx_tipo_orden = 14; //recoger..
                        $msn = ' Recoger  ' . $rowNotProcessed->date_recoger_orden_pedido;
                        $date_recoger = $rowNotProcessed->date_recoger_orden_pedido;
                    else:
                        $ventamaxx_tipo_orden = 13; //reparto..
                        $msn = '';
                        $date_recoger = '';

                    endif;
                    //validate ayden..
                    if($rowNotProcessed->id_tipo_pago==$Constans['PAGO_CON_AYDEN'] ):
                       $ventamaxx_tipo_orden=15;
                    endif;

                    /* CLIENT INFORMATION.. */
                    $data['id_cliente'] = $rowNotProcessed->id_cliente;
                    
                    $arr_tmp_client = Client::getClientNoAddressById($data);
                    //dd($arr_tmp_client);
                    $array_data_client=$this->parsingDataRecordClient($arr_tmp_client);                                    
                    //$arr_cliente = Client::getClientById($data);
                       /* default address */
                    //var_dump($arr_tmp_client);
                    $arrayAddressClient['id_tipo_orden'] = $rowNotProcessed->id_tipo_orden;
                    $arrayAddressClient['id_cliente'] = $rowNotProcessed->id_cliente;
                    $arrayAddressClient['id_rel_domicilio_cliente'] =  $rowNotProcessed->id_rel_domicilio_cliente;
                    $arrayAddressClient['celular_cliente']=$arr_tmp_client[0]->celular_cliente;
                    $arrayAddressClient['domicilio_cliente']=$arr_tmp_client[0]->domicilio_cliente;
                    $arrayAddressClient['numero_ext_cliente']=$arr_tmp_client[0]->numero_ext_cliente;
                    $arrayAddressClient['numero_int_cliente']=$arr_tmp_client[0]->numero_int_cliente;
                    $arrayAddressClient['cp_cliente']=$arr_tmp_client[0]->cp_cliente;                   
                    $arrayAddressClient['referencia_domicilio_cliente']=$arr_tmp_client[0]->referencia_domicilio_cliente;
                    $arrayAddressClient['colonia_garantia_ube']='';    
                    $arrayAddressClient['app_colonias_garantia_ube']=$arr_tmp_client[0]->app_colonias_garantia_ube; 
                    //dd($arrayAddressClient);
                    $arrayAddress = $this->getAddressClientDeliver($arrayAddressClient);
                    //dd($arrayAddress);
                    //FIN DE DATOS DEL CLIENTE..
                   // $msgGarantia = ($arr_name_client[0]->id_status_garantia_ube == $Constans['STATUS_CON_GARANTIA']) ? 'T30' : '';
                    $json_arr['orden'][$cont_orden]['id_unidad'] = $idUbe; //cambiar a 99999
                    $json_arr['orden'][$cont_orden]['status_procesada_orden'] = "ban_procesado_orden_pedido";
                    $json_arr['orden'][$cont_orden]['fecha_procesada_orden'] = "date_procesado_orden_pedido";
                    $json_arr['orden'][$cont_orden]['id_orden'] = $rowNotProcessed->id_procesado_orden_pedido;
                    $json_arr['orden'][$cont_orden]['total'] = $rowNotProcessed->total_compra_orden_pedido;
                    $json_arr['orden'][$cont_orden]['impuesto'] = $iva;
                    $json_arr['orden'][$cont_orden]['tipo_orden'] = $ventamaxx_tipo_orden; //mostrador_recoger..
                    //$json_arr['orden'][$cont_orden]['tipo_orden']        
                    $json_arr['orden'][$cont_orden]['pago'] = $rowNotProcessed->pago_cliente_orden_pedido;
                    $json_arr['orden'][$cont_orden]['tipo_pago'] = $rowNotProcessed->descripcion_vmx_tipo_pago;
                    $json_arr['orden'][$cont_orden]['comentario'] = trim($arrayAddress['referencia_domicilio_cliente']).' '.trim($arrayAddress['msgGarantia'] . ' ' . $rowNotProcessed->comentario_orden_pedido . ' ' . $msn);
                    $json_arr['orden'][$cont_orden]['delivery_time'] = $date_recoger;
                    $json_arr['orden'][$cont_orden]['ban_app_orden'] = 1;
                 
                    $json_arr['orden'][$cont_orden]['cliente']['etiqueta'] = '';
                    $json_arr['orden'][$cont_orden]['cliente']['oficina_hotel'] = '';
                    $json_arr['orden'][$cont_orden]['cliente']['nombre_cliente'] = $this->strip_tildes(stripslashes(trim($array_data_client['nombre_cliente'])));
                    $json_arr['orden'][$cont_orden]['cliente']['apellido_cliente'] = $this->strip_tildes(stripslashes(trim($array_data_client['apellido_cliente'])));
                   
                    $json_arr['orden'][$cont_orden]['cliente']['nombre_calle'] = $this->strip_tildes(stripcslashes(trim($arrayAddress['domicilio_cliente'])));
                    $json_arr['orden'][$cont_orden]['cliente']['numext_domicilio'] = trim($arrayAddress['numero_ext_cliente']);
                    $json_arr['orden'][$cont_orden]['cliente']['numint_domicilio'] = trim($arrayAddress['numero_int_cliente']);
                    
                    $json_arr['orden'][$cont_orden]['cliente']['id_colonia'] = (strlen(stripcslashes(trim($arrayAddress['colonia_garantia_ube'])))>0)?stripcslashes(trim($arrayAddress['colonia_garantia_ube'])):stripcslashes(trim($arrayAddress['app_colonias_garantia_ube']));
                    $json_arr['orden'][$cont_orden]['cliente']['id_codigopostal'] = stripcslashes(trim($arrayAddress['cp_garantia_ube']));
                    $json_arr['orden'][$cont_orden]['cliente']['referencia_domicilio'] = trim($this->strip_tildes(stripcslashes(trim($arrayAddress['referencia_domicilio_cliente']))));
                    $json_arr['orden'][$cont_orden]['cliente']['email'] = trim($array_data_client['email_cliente']);
                    //VALIDAR TELEFONO DEL CLIENTE// si existe celular,.,. mando el celular..
                    $json_arr['orden'][$cont_orden]['cliente']['id_telefono'] = $arrayAddress['id_telefono'];
                    $json_arr['orden'][$cont_orden]['cliente']['id_tipotelefono'] = $arrayAddress['id_tipotelefono'];
                    $id_procesado_orden_pedido = $rowNotProcessed->id_procesado_orden_pedido;
                    $cont_producto = 0;

                    $data['id_procesado_orden_pedido'] = $id_procesado_orden_pedido;
                                       
                    $arrayProducts = Processor::getProductsbyOrder($data);

                    if (count($arrayProducts) > 0) {

                        foreach ($arrayProducts as $rowProduct) {
                            $cont_recetas_full = 0;
                            $cont_recetas_left = 0;
                            $cont_recetas_right = 0;
                            $id_paquete_aux = 0;
                            /*4x4*/
                            $cont_recetas_left4x4 = 0;
                            $cont_recetas_middle14x4 = 0;
                            $cont_recetas_middle24x4 = 0;
                            $cont_recetas_right4x4 = 0;
                            $cont_recetas_base4x4=0;
                            $procesado_aplica_desc_articulo_pedido_aux = $rowProduct->procesado_aplica_desc_articulo_pedido;
                            // var_dump($procesado_aplica_desc_articulo_pedido_aux);
                            $id_articulo_procesada_orden = $rowProduct->id_articulo;
                            $precio_paquete = $rowProduct->precio_articulo_pedido;
                            $categoryProduct = Product::where('id_articulo', $rowProduct->id_articulo)->get();                         
                            $precio_paquete = $rowProduct->precio_articulo_pedido;
                            $nombre_producto = $this->strip_tildes($rowProduct->web_nombre_articulo);
                            $nombre_paquete = $this->strip_tildes($rowProduct->web_nombre_articulo);

                            if ($categoryProduct[0]->id_categoria == $Constans['ESPECIALIDADES'] || $categoryProduct[0]->id_categoria == $Constans['PIZZAS']) {
                                //ESTABLECER VARIABLES DEL IVA
                                ////////////////////////////////
                             
                                if (Store::getStatusTaxStore($data) > 0):
                                    //calculos para el cobro del iva..
                                    $cant_sin_iva = ($rowProduct->precio_articulo_pedido / 1.16);
                                    $cant_iva_round = ($cant_sin_iva * 100) / 100;
                                    //desgloce..
                                    $iva_float = $cant_iva_round * 0.16;
                                    $iva = ($iva_float * 100) / 100;
                                    $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];
                                else:
                                    $iva = 0;
                                    $impporc_producto = 0;
                                endif;

                                if ($rowProduct->ban_aplica_codigo_promocion_orden_pedido == $Constans['VIGENTE']) {
                                    //aca,, buscar articulo del cupon y calcular descuento 
                                    $dataCode['id_procesado_articulo_pedido'] = $rowProduct->id_procesado_articulo_pedido;
                                    $dataCode['id_articulo'] = $rowProduct->id_articulo;
                                    $dataCode['id_tamano_articulo'] = $rowProduct->id_tamano_articulo;

                                    $arrayDiscount = Processor::getInfoDiscountCodeById($dataCode);
                                    //poner todos los valores inicializados..
                                    /**/
                                    $precio_paquete = $rowProduct->precio_articulo_pedido;
                                    $precio_dm_paquete = $rowProduct->precio_articulo_pedido;
                                    $descripcion_esquemacobro = "cobro general";
                                    $id_esquemacobro = 1;

                                    if (count($arrayDiscount) > 0) {

                                        //recalcular el iva y precio_producto....
                                        //$precio_promocion_articulo=$clOnline->precio_articulo_pedido;
                                        $precio_paquete = $rowProduct->precio_articulo_pedido - $arrayDiscount[0]->monto_descuento_procesado_orden_codigo_promocion;
                                        $descuento_paquete = $rowProduct->precio_articulo_pedido - $precio_paquete;
                                        $precio_producto = $rowProduct->precio_articulo_pedido;
                                        $precio_dm_paquete = $precio_producto - $descuento_paquete;
                                        $descripcion_esquemacobro = "DESCUENTO PEDIDO EN LINEA";
                                        $id_esquemacobro = $Constans['ESQUEMACOBRO_DESCUENTO_PEDIDO_EN_LINEA'];
                                        /* CALCULO DE IVAS */
                                        //*RECALCULO TODO*/
                                        if (Store::getStatusTaxStore($data) > 0 || $categoryProduct[0]->id_categoria == $Constans['BEBIDAS']):
                                            //calculos para el cobro del iva..
                                            //IVA DESGLOZADO..
                                            $cant_sin_iva = ($precio_paquete / 1.16);
                                            // $cant_iva_round = round($cant_sin_iva * 100) / 100;
                                            $cant_iva_round = $cant_sin_iva * 100 / 100;
                                            //desgloce..
                                            $iva_float = $cant_iva_round * 0.16;
                                            // $iva = round($iva_float * 100) / 100;
                                            $iva = ($iva_float * 100) / 100;
                                            $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];

                                        else:
                                            $iva = 0;
                                            $impporc_producto = 0;
                                        endif;
                                    }
                                }
                                else {
                                    $precio_paquete = $rowProduct->precio_articulo_pedido;
                                    $precio_dm_paquete = $rowProduct->precio_articulo_pedido;
                                    $descripcion_esquemacobro = "cobro general";
                                    $id_esquemacobro = 1;
                                    $precio_dm_paquete = $rowProduct->precio_articulo_pedido;
                                }
                                
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_producto'] = $cont_producto + 1;
                                
                                if ($rowProduct->app_porcion_armado_pizza == 4) {
                                    //IF PIZZA BUILDER SIZE CANT BUILD ON 4 PORTIONS, THEN SENDS THE OTHER VENTAMAXX SIZE
                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_tamano'] = $rowProduct->app_id_porciones_tamanno_ventamaxx;
                                } else {
                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_tamano'] = $rowProduct->procesado_id_tamanno_ventamaxx;
                                }

                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['cantidad_producto'] = $rowProduct->cantidad_articulo_pedido;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['comment_prodducto'] = '';
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['descripcion_prodducto'] = $rowProduct->web_descripcion_tamano_articulo . ' ' . $rowProduct->web_nombre_articulo;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $rowProduct->precio_articulo_pedido . '.00';
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $precio_dm_paquete;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_esquemacobro'] = $id_esquemacobro; //ocupo el esquema cobro de cada paquete..
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['esquemacobro_producto'] = $descripcion_esquemacobro; //descripcion del paquete..
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto;
                                //obtener todos los datos de la config de la pizza..
                                $dataBuilder['id_procesado_articulo_pedido'] = $rowProduct->id_procesado_articulo_pedido;
                                $arrayPizzaBuilder = Processor::getProcesadoOrderPizzaBuilder($dataBuilder);
                               
                                //validar especialidad..
                                if (count($arrayPizzaBuilder) > 0){
                                   //  dd($rowProduct);
                                    $receta_especialidad = $rowProduct->procesado_id_receta_ventamaxx;
                                    //poner la reseta base..
                                    $reseta_base = ($arrayPizzaBuilder[0]->salsa == 1) ? $Constans['RECETA_BARBECUE'] : $Constans['RECETA_SAPRE'];
                                    //DESLACTOSADO
                                    $reseta_extra_queso = ($arrayPizzaBuilder[0]->base_chesse_desclac == 1) ? $Constans['RECETA_QUESO_DESLACTOSADO'] : $Constans['RECETA_QUESO'];
                                    $cont_receta_queso = 1;
                                    //FIN DE DESLACTOSADO
                                    //RESETA BASE..
                                    if ($rowProduct->app_porcion_armado_pizza == 4) {
                                        
                                        $id_porcion = 3;
                                        for ($i = 0; $i <= 3; $i++) {

                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $reseta_base;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = 1;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'SALSA';
                                            $id_porcion++;
                                        }
                                        $cont_recetas_base4x4++;
                                       
                                    }
                                    else{
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 0;
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $reseta_base;
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'SALSA';
                                        $cont_recetas_full++;
                                    }
                                    
                                   
                                    if ($categoryProduct[0]->id_categoria == $Constans['ESPECIALIDADES'] && $rowProduct->id_articulo != $Constans['PIZZA_BASE_ESP']):

                                        //INCLUIR LA RECETA DE LA ESPECIALIDAD..                                       
                                        if ($receta_especialidad > 0) {
                                            if ($rowProduct->app_porcion_armado_pizza == 4) {
                                                $id_porcion = 3;
                                                for ($i = 0; $i <= 3; $i++) {

                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $receta_especialidad;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = 1;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = $rowProduct->nombre_articulo;
                                                    $id_porcion++;
                                                }
                                                $cont_recetas_base4x4++;
                                            } else {

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $receta_especialidad;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = $rowProduct->nombre_articulo;
                                                $cont_recetas_full++;
                                            }
                                        }
                                    endif;
                                    //SI TRAE QUESO DESLACTOSADO INCREMENTO LA RECETA DE QUESO EN 2..
                                    //CHECAR SI TIENE QUESO DESLACTOSADO Y AUMENTAR RECETA DE QUESO EN 1
                                    if ($arrayPizzaBuilder[0]->extra_chesse == 1):
                                        
                                        if ($rowProduct->app_porcion_armado_pizza == 4) {
                                            $id_porcion = 3;
                                            for ($i = 0; $i <= 3; $i++) {

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $reseta_extra_queso;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = $cont_receta_queso;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Con extraqueso';
                                                $id_porcion++;
                                            }
                                            $cont_recetas_base4x4++;
                                        } else {

                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $reseta_extra_queso;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = $cont_receta_queso;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Con extraqueso';

                                            $cont_recetas_full++;
                                        }
                                    endif;

                                    if ($rowProduct->app_porcion_armado_pizza < 4) {
                                    
                                        if ($arrayPizzaBuilder[0]->sin_ing_1 > 0):
                                            $arrayIngredient = Ingredient::where('id_ingrediente_pizza', $arrayPizzaBuilder[0]->sin_ing_1)->get();
                                            $nombre_ingrediente = $arrayIngredient[0]->descripcion_ingrediente_pizza;
                                            $id_receta_ventamaxx = $arrayIngredient[0]->id_receta_ventamaxx;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $id_receta_ventamaxx;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = -1;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = $nombre_ingrediente;
                                            $cont_recetas_full++;

                                        endif;

                                        if ($arrayPizzaBuilder[0]->sin_ing_2 > 0):

                                            $arrayIngredient = Ingredient::where('id_ingrediente_pizza', $arrayPizzaBuilder[0]->sin_ing_2)->get();
                                            $nombre_ingrediente = $arrayIngredient[0]->descripcion_ingrediente_pizza;
                                            $id_receta_ventamaxx = $arrayIngredient[0]->id_receta_ventamaxx;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $id_receta_ventamaxx;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = -1;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = $nombre_ingrediente;
                                            $cont_recetas_full++;

                                        endif;
                                    }

                                    if ($arrayPizzaBuilder[0]->borde < 0):
                                        
                                        if ($rowProduct->app_porcion_armado_pizza == 4) {
                                            $id_porcion = 3;
                                            for ($i = 0; $i <= 3; $i++) {

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $Constans['RECETA_AJOJOLI'];
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = -1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Sin ajojolí';
                                                $id_porcion++;
                                            }
                                            $cont_recetas_base4x4++;
                                        } else{

                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $Constans['RECETA_AJOJOLI'];
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = -1;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Sin ajojolí';

                                            $cont_recetas_full++;
                                        }

                                    endif;

                                    if ($arrayPizzaBuilder[0]->orilla_queso == $Constans['CON_ORILLA']):

                                        if ($rowProduct->app_porcion_armado_pizza == 4) {
                                            $id_porcion = 3;
                                            for ($i = 0; $i <= 3; $i++) {
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $Constans['RECETA_ORILLA_QUESO'];
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Orilla de queso';

                                                $id_porcion++;
                                            }
                                            $cont_recetas_base4x4++;
                                        } else {

                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $Constans['RECETA_ORILLA_QUESO'];
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Orilla de queso';
                                            $cont_recetas_full++;
                                        }

                                    endif;

                                    if ($arrayPizzaBuilder[0]->pan_cruji == $Constans['CON_PANCRUJI']):
                                        if ($rowProduct->app_porcion_armado_pizza == 4) {
                                            $id_porcion = 3;
                                            for ($i = 0; $i <= 3; $i++) {
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $Constans['RECETA_PAN_CRUJI'];
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Pan cruji';

                                                $id_porcion++;
                                            }
                                            $cont_recetas_base4x4++;
                                        } else {

                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $Constans['RECETA_PAN_CRUJI'];
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Pan cruji';
                                            $cont_recetas_full++;
                                        }
                                    endif;
                                    //checar si hay especialidades en las pizzas armadas. izquierda o derecha..
                                 
                                    if ($arrayPizzaBuilder[0]->ban_mitad_esp > 0){
                                        if ($arrayPizzaBuilder[0]->id_esp_left_builder > 0):
                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 1;
                                            }

                                            $arraySpeciality = Product::where('id_articulo', $arrayPizzaBuilder[0]->id_esp_left_builder)->get();
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['id_receta'] = $arraySpeciality[0]->id_art_receta_ventamaxx;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['cantidad_receta'] = 1;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['descripcion_receta'] = $arraySpeciality[0]->web_nombre_articulo;

                                            $cont_recetas_left++;
                                        endif;

                                        if ($arrayPizzaBuilder[0]->id_esp_right_builder > 0):

                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 2;
                                            }


                                            $arraySpeciality = Product::where('id_articulo', $arrayPizzaBuilder[0]->id_esp_right_builder)->get();

                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['id_receta'] = $arraySpeciality[0]->id_art_receta_ventamaxx;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['cantidad_receta'] = 1;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['descripcion_receta'] = $arraySpeciality[0]->web_nombre_articulo;

                                            $cont_recetas_right++;


                                        endif;
                                    }
                                    
                                    else
                                    if ($arrayPizzaBuilder[0]->ban_cuartos_esp > 0 || $rowProduct->app_porcion_armado_pizza == 4) {
                                         /* si es 4x4 */
                                        /* aqui cuartos.. */
                                            $cont_recetas_left4x4 = $cont_recetas_base4x4; //return the last position of base recipe
                                            $cont_recetas_middle14x4 = $cont_recetas_base4x4;
                                            $cont_recetas_middle24x4 = $cont_recetas_base4x4;
                                            $cont_recetas_right4x4 = $cont_recetas_base4x4;                                           
                                            if ($arrayPizzaBuilder[0]->id_esp_left4x4_builder > 0) {
                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0])) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 3;
                                                }

                                                $arrayRecipe = Product::where('id_articulo', $arrayPizzaBuilder[0]->id_esp_left4x4_builder)->get();
                                                $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                //$id_receta_ventamaxx = $clOnline->get_receta_vmx_by_id_articulo($clOnline->id_esp_left_builder);
                                                // $nombre_ingrediente = $clOnline->get_nombre_articulo_by_parameter($clOnline->id_esp_left_builder);
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['id_receta'] = $id_receta_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['descripcion_receta'] = $nombre_ingrediente;

                                                $cont_recetas_left4x4++;
                                                
                                               
                                            }
                                            
                                            if ($arrayPizzaBuilder[0]->id_esp_middle14x4_builder > 0) {

                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 4;
                                                }

                                                $arrayRecipe = Product::where('id_articulo', $arrayPizzaBuilder[0]->id_esp_middle14x4_builder)->get();
                                                $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['id_receta'] = $id_receta_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['descripcion_receta'] = $nombre_ingrediente;

                                                $cont_recetas_middle14x4++;
                                            }
                                            
                                            if ($arrayPizzaBuilder[0]->id_esp_middle24x4_builder > 0) {

                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 5;
                                                }

                                                $arrayRecipe = Product::where('id_articulo', $arrayPizzaBuilder[0]->id_esp_middle24x4_builder)->get();
                                                $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['id_receta'] = $id_receta_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['descripcion_receta'] = $nombre_ingrediente;

                                                $cont_recetas_middle24x4++;
                                            }
                                            
                                            if ($arrayPizzaBuilder[0]->id_esp_right4x4_builder > 0) {

                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3])) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['id_porcion'] = 6;
                                                }

                                                $arrayRecipe = Product::where('id_articulo', $arrayPizzaBuilder[0]->id_esp_right4x4_builder)->get();
                                                $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['id_receta'] = $id_receta_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['descripcion_receta'] = $nombre_ingrediente;

                                                $cont_recetas_right4x4++;
                                            }
                                                                                    
                                    } //END. IF 4X4 OR BAN_CUARTOS=1

                                    //precio DM PARA ARTICULO SIN ESQUEMACOBRO..
                                    if ($procesado_aplica_desc_articulo_pedido_aux == 1):

                                        //$json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $clOnline->precio_articulo_pedido;
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_esquemacobro'] = 967; //ocupo el esquema cobro de cada paquete..
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['esquemacobro_producto'] = '50% DESCUENTO REDES SOCIALES'; //descripcion del paquete..
                                    //  $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                    // $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto
                                    endif;

                                    //25%DESC

                                    if ($procesado_aplica_desc_articulo_pedido_aux == 2): //promo 25 desc
                                        //$json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $clOnline->precio_articulo_pedido;
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_esquemacobro'] = 994; //ocupo el esquema cobro de cada paquete..
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['esquemacobro_producto'] = '25% DESCUENTO PIDE EN LINEA'; //descripcion del paquete..
                                    //  $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                    // $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto
                                    endif;
                                    
                                    //DESCUENTO PIZZA GRATIS
                                    
                                     if ($procesado_aplica_desc_articulo_pedido_aux == 3):
                                         
                                        if ($rowProduct->cantidad_articulo_pedido == 1):
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = 0;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_esquemacobro'] = $Constans['ESQUEMACOBRO_APP_PIZZA_GRATIS']; //ocupo el esquema cobro de cada paquete..
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['esquemacobro_producto'] = '2DA PIZZA GRATIS APP'; //descripcion del paquete..
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = 0;
                                        // $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto
                                        elseif($rowProduct->cantidad_articulo_pedido > 1) :
                                        
                                            $descuento_paquete = round($rowProduct->precio_articulo_pedido / $rowProduct->cantidad_articulo_pedido);
                                            $precio_producto = $rowProduct->precio_articulo_pedido;
                                            $precio_dm_paquete = $precio_producto - $descuento_paquete;
                                        
                                            $precio_paquete = $rowProduct->precio_articulo_pedido -$descuento_paquete;
                                    
                                            /* CALCULO DE IVAS */
                                            //*RECALCULO TODO*/
                                            if (Store::getStatusTaxStore($data) > 0 || $categoryProduct[0]->id_categoria == $Constans['BEBIDAS']):
                                                //calculos para el cobro del iva..
                                                //IVA DESGLOZADO..
                                                $cant_sin_iva = ($precio_paquete / 1.16);
                                                // $cant_iva_round = round($cant_sin_iva * 100) / 100;
                                                $cant_iva_round = $cant_sin_iva * 100 / 100;
                                                //desgloce..
                                                $iva_float = $cant_iva_round * 0.16;
                                                // $iva = round($iva_float * 100) / 100;
                                                $iva = ($iva_float * 100) / 100;
                                                $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];

                                            else:
                                                $iva = 0;
                                                $impporc_producto = 0;
                                            endif;
                                            
                                            
                                        
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_esquemacobro'] = $Constans['ESQUEMACOBRO_APP_PIZZA_GRATIS']; //ocupo el esquema cobro de cada paquete..
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['esquemacobro_producto'] = '2DA PIZZA GRATIS APP'; //descripcion del paquete..
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $precio_dm_paquete;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto;
                                        endif;
                                    endif;


                                    //CONSULTAR LA CONFIG DE LA PIZZA BUILDER..
                                    $dataConfigPizza['id_procesado_orden_pizza_builder'] = $arrayPizzaBuilder[0]->id_procesado_orden_pizza_builder;

                                    $arrayCnfPizza = Processor::getProcesadoOrderConfigPizzabyId($dataConfigPizza);
                                    if (count($arrayCnfPizza) > 0) {

                                        foreach ($arrayCnfPizza as $rowConfPizza) {

                                            $arrayIngredient = Ingredient::where('id_ingrediente_pizza', $rowConfPizza->id_ingrediente_pizza)->get();
                                            $nombre_ingrediente = $arrayIngredient[0]->descripcion_ingrediente_pizza;
                                            $id_receta_ventamaxx = $arrayIngredient[0]->id_receta_ventamaxx;

                                            //ACOMODAR EN LA PORCION.
                                            // 0 = COMPLETA, 1 = LEFT, 2 =  RIGHT..
                                            
                                            //IF QUARTERS ..
                                          //  dd($arrayPizzaBuilder);
                                            if ($arrayPizzaBuilder[0]->ban_cuartos_esp > 0 || $rowProduct->app_porcion_armado_pizza == 4) {

                                                if ($rowConfPizza->porcion_ingrediente_pizza == 'left4x4') {
                                                    //CHECAR SI EXISTE id_porcion
                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0])) {
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 3;
                                                    }

                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['id_receta'] = $id_receta_ventamaxx;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['cantidad_receta'] = $rowConfPizza->cantidad_ingrediente_pizza;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['descripcion_receta'] = $nombre_ingrediente;

                                                    $cont_recetas_left4x4++;
                                                } else
                                                if ($rowConfPizza->porcion_ingrediente_pizza == 'middle14x4') {

                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 4;
                                                    }

                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['id_receta'] = $id_receta_ventamaxx;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['cantidad_receta'] = $rowConfPizza->cantidad_ingrediente_pizza;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['descripcion_receta'] = $nombre_ingrediente;

                                                    $cont_recetas_middle14x4++;
                                                } else
                                                if ($rowConfPizza->porcion_ingrediente_pizza == 'middle24x4') {

                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 5;
                                                    }

                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['id_receta'] = $id_receta_ventamaxx;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['cantidad_receta'] = $rowConfPizza->cantidad_ingrediente_pizza;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['descripcion_receta'] = $nombre_ingrediente;

                                                    $cont_recetas_middle24x4++;
                                                } else
                                                if ($rowConfPizza->porcion_ingrediente_pizza == 'right4x4') {

                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3])) {
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['id_porcion'] = 6;
                                                    }

                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['id_receta'] = $id_receta_ventamaxx;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['cantidad_receta'] = $rowConfPizza->cantidad_ingrediente_pizza;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['descripcion_receta'] = $nombre_ingrediente;

                                                    $cont_recetas_right4x4++;
                                                }
                                            } else { ///

                                                if ($rowConfPizza->porcion_ingrediente_pizza == 'left') {
                                                    //CHECAR SI EXISTE id_porcion
                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 1;
                                                    }

                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['id_receta'] = $id_receta_ventamaxx;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['cantidad_receta'] = $rowConfPizza->cantidad_ingrediente_pizza;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['descripcion_receta'] = $nombre_ingrediente;

                                                    $cont_recetas_left++;
                                                } else
                                                if ($rowConfPizza->porcion_ingrediente_pizza == 'right') {

                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 2;
                                                    }

                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['id_receta'] = $id_receta_ventamaxx;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['cantidad_receta'] = $rowConfPizza->cantidad_ingrediente_pizza;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['descripcion_receta'] = $nombre_ingrediente;

                                                    $cont_recetas_right++;
                                                } else {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $id_receta_ventamaxx;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = $rowConfPizza->cantidad_ingrediente_pizza;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = $nombre_ingrediente;

                                                    $cont_recetas_full++;
                                                }
                                            }//AQUI ELSE..
                                        }//endforeachConfigPizza..
                                    }

                                    //  echo $clOnline->sin_ing_1;
                                //  echo '<br>'.$clOnline->salsa;
                                //$clOnline->id_procesado_articulo_pedido = $id_procesado_articulo_pedido;
                                }  //FIN DE SI HAY CONFIGURACION EN PIZZA BUILDER
                            } //fin de especialiaddes O PIZZAS
                            else
                            if ($categoryProduct[0]->id_categoria == $Constans['PAQUETES'] || $procesado_aplica_desc_articulo_pedido_aux) {
                               
                                if ($categoryProduct[0]->id_categoria_promocion == $Constans['PRODUCTOS_VARIOS'] ||
                                    $categoryProduct[0]->id_categoria_promocion == $Constans['ARMADO_4X4_BASICO'] ||
                                    $categoryProduct[0]->id_categoria_promocion == $Constans['ARMADO_4X4_ESPECIALIDAD']) {
                                    /*  'PRODUCTOS_VARIOS'=>1,
                                      'ARMADO_4X4_BASICO'=>4,
                                      'ARMADO_4X4_ESPECIALIDAD'=>5, */
                                    $cont_recetas_sub_full = 0;
                                    $cont_recetas_sub_left = 0;
                                    $cont_recetas_sub_right = 0;
                                    $cont_receta_queso = 1;
                                    //variables..
                                    $cont_recetas_full = 0;
                                    $cont_recetas_left = 0;
                                    $cont_recetas_right = 0;
                                    $id_paquete_aux = 0;
                                    /* 4x4 */
                                    $cont_recetas_left4x4 = 0;
                                    $cont_recetas_middle14x4 = 0;
                                    $cont_recetas_middle24x4 = 0;
                                    $cont_recetas_right4x4 = 0;
                                    $cont_recetas_base4x4 = 0;

                                    $totalPrecioLista = 0;
                                    $cant_pedido = $rowProduct->cantidad_articulo_pedido;
                                    //  $resultConfigPaquete = $clOnline->get_procesado_procesado_orden_config_paquete_by_id();
                                    $dataPackage['id_procesado_articulo_pedido'] = $rowProduct->id_procesado_articulo_pedido;
                                    $arrayConfigPackage = Processor::getOrderConfigPackageById($dataPackage);
                                    ////@ $totalPrecioLista = $clOnline->get_total_precio_orden_config_paquete_by_id() * $cant_pedido;
                                    // $totalPrecioLista = $clOnline->get_total_precio_orden_config_paquete_by_id();
                                    $totalPrecioLista = Processor::getTotalOrderConfigPackageByid($dataPackage) * $cant_pedido;
                        
                                    $descuento_paquete = $totalPrecioLista - $precio_paquete;

                                    // while ($arrayConfigPaquete = mysql_fetch_array($resultConfigPaquete)) {
                                    foreach ($arrayConfigPackage as $rowConfPackage) {

                                        $categoryItem = Category::where('id_categoria', $rowConfPackage->id_categoria)->get();

                                        $dataProduct['id_articulo'] = $rowConfPackage->id_articulo;
                                        $dataProduct['id_tamano_articulo'] = $rowConfPackage->id_tamano_articulo;
                                        $arrayProduct = Product::getProductPrice($dataProduct);
                                        
                                        $precio_producto = $arrayProduct[0]->precio_articulo_tamano * $cant_pedido;                                      
                                        //VALIDATE IF DISCOUNT IS ACTIVE FOR CATEGORY ITEM 
                                        //CUADRAR EL PRECIO DE LA PIZZABASE+INC ESPECIALIDAD
                                        
                                        if($rowConfPackage->id_articulo==$Constans['PIZZA_BASE_ESP']){
                                            
                                              $dataProduct['id_articulo'] = $Constans['PIZZA_BASE'];
                                              $dataProduct['id_tamano_articulo'] = $rowConfPackage->id_tamano_articulo;
                                              $arrayProductTemp = Product::getProductPrice($dataProduct);                                           
                                              $precio_producto += $arrayProductTemp[0]->precio_articulo_tamano * $cant_pedido;
                                             //cambio el total precio lista..
                                              $totalPrecioLista+=$arrayProductTemp[0]->precio_articulo_tamano * $cant_pedido;
                                             //cambio descuento..
                                              $descuento_paquete = $totalPrecioLista - $precio_paquete; 
                                        }
                                        
                                        //  if (($RowPromo->id_articulo == $Constans['PIZZA_BASE'] || $RowPromo->id_articulo == $Constans['PIZZA_BASE_ESP']) ) {
                                        
                                        if ($categoryItem[0]->ban_descuento_paquete_categoria == 1) {
                                            
                                            $precio_dm_paquete = $precio_producto - $descuento_paquete;
                                            
                                           // dd($precio_producto);
                                        } else {

                                            //MULTIPLICAR POR LA CANTIDAD DEL ARTICULO POR QUE EN VENTAMAXX NO CUADRABA EL PRECIO.. 
                                            //@$precio_producto = ($clOnline->get_price_by_id_article() * $cant_pedido)*$clOnline->cantidad_articulo;
                                            $precio_producto = $precio_producto * $rowConfPackage->cantidad_articulo; //lo mando solo multiplicado por su cantidad..
                                            //$precio_dm_paquete = $precio_producto-$descuento_paquete;

                                            $precio_dm_paquete = $precio_producto;
                                        }

                                        //echo 'precio producto'.$precio_producto;
                                        //$precio_dm_paquete = $precio_producto-$descuento_paquete;
                                        //$cant_sin_iva = ($precio_producto/ 1.16);
                                        if (Store::getStatusTaxStore($data) > 0 || $rowConfPackage->id_categoria == $Constans['BEBIDAS']):
                                            //calculos para el cobro del iva..
                                            $cant_sin_iva = ($precio_dm_paquete / 1.16);
                                            $cant_iva_round = ($cant_sin_iva * 100) / 100;
                                            //desgloce..
                                            $iva_float = $cant_iva_round * 0.16;
                                            $iva = ($iva_float * 100) / 100;
                                            $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];

                                        else:
                                            $iva = 0;
                                            $impporc_producto = 0;
                                        endif;
                                     //  dd($rowConfPackage);
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_producto'] = $cont_producto + 1;
                                        
                                         if ($rowConfPackage->app_porcion_armado_pizza == 4) {
                                            //IF PIZZA BUILDER SIZE CANT BUILD ON 4 PORTIONS, THEN SENDS THE OTHER VENTAMAXX SIZE
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_tamano'] = $rowConfPackage->app_id_porciones_tamanno_ventamaxx;
                                        } else {
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_tamano'] = $rowConfPackage->config_paquete_id_tamanno_ventamaxx;
                                        }
                                        //dd($rowConfPackage->cantidad_articulo);    
                                        //$cant_pedido*rowConfPackage->cantidad_articulo;
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['cantidad_producto'] = $cant_pedido*$rowConfPackage->cantidad_articulo;
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['comment_prodducto'] = ' ';
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['descripcion_prodducto'] = $rowConfPackage->web_descripcion_tamano_articulo . ' ' . $rowConfPackage->web_nombre_articulo;

                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $precio_producto . '.00';
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $precio_producto;
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_esquemacobro'] = $rowConfPackage->config_id_esquema_cobro_ventamaxx; //ocupo el esquema cobro de cada paquete..
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['esquemacobro_producto'] = $nombre_paquete; //descripcion del paquete..
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto;
                                        //BUSCAR EN CONFIG DE LA PIZZA..
                                        //tabla categoria = $categoryItem
                                        if ($categoryItem[0]->ban_descuento_paquete_categoria == 1) {
                                            //ban_descuento_paquete_categoria INDICA EL DESCUENTO PARA PRODUCTOS CON PRECIOS MAYORES. 
                                            //ES AL QUE SE LE HARÀ EL  DESCUENTO DEL PAQUETE.
                                            //BUSCAR RECETAS EN CONFIG PIZZA..
                                            //nueva
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $precio_dm_paquete . '.00';                                      
                                           //---
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $precio_dm_paquete;

                                            if ($categoryItem[0]->receta_base_ventamaxx > 0) {
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 0;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = RECETA_BASE_BAGUETTES;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Receta base';
                                                $cont_recetas_full++;
                                            } else if ($rowConfPackage->id_categoria == $Constans['ESPECIALIDADES'] || $rowConfPackage->id_categoria == $Constans['PIZZAS']) {
                                                
                                                $dataBuilder['id_procesado_articulo_pedido'] = $rowConfPackage->id_procesado_articulo_pedido;
                                                $arrayBuilder = Processor::getConfigBuilderById($dataBuilder);                                               
                                                $reseta_base = ($arrayBuilder[0]->salsa == 1) ? $Constans['RECETA_BARBECUE'] : $Constans['RECETA_SAPRE'];

                                                if ($rowConfPackage->app_porcion_armado_pizza == 4) {
                                                    $id_porcion = 3;
                                                    for ($i = 0; $i <= 3; $i++) {

                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $reseta_base;
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = 1;
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'SALSA';
                                                        $id_porcion++;
                                                    }
                                                    $cont_recetas_base4x4++;
                                                } else {

                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 0;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $reseta_base;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'SALSA';
                                                    $cont_recetas_full++;
                                                }
                                            }

                                            if ($rowConfPackage->id_categoria == $Constans['ESPECIALIDADES'] || $rowConfPackage->id_categoria == $Constans['PIZZAS']) {

                                                $dataBuilder['id_procesado_articulo_pedido'] = $rowConfPackage->id_procesado_articulo_pedido;
                                                $arrayBuilder = Processor::getConfigBuilderById($dataBuilder);
                                               
                                                $reseta_extra_queso = ($arrayBuilder[0]->base_chesse_desclac == 1) ? $Constans['RECETA_QUESO_DESLACTOSADO'] : $Constans['RECETA_QUESO'];
                                                //RESETA BASE.. //SI ES BAGUETTE SE CAMBIA LA RECETA BASE..
                                                if ($arrayBuilder[0]->extra_chesse == 1):                                                   
                                                    
                                                    $dataPrice['id_ingrediente_pizza'] = $Constans['EXTRA_CHESSE'];
                                                    $dataPrice['id_tamano_articulo'] = $rowConfPackage->id_tamano_articulo;
                                                    $arrayPrice = Product::getPriceExtrasByParams($dataPrice);
                                         
                                                    $precio_extra_chessse = $arrayPrice[0]->precio_ingrediente_tamano;
                                                    $precio_extra_chessse = $precio_extra_chessse * $cant_pedido;
                                                    //var_dump($precio_extra_chessse);
                                                    $totalPrecioLista+=$precio_extra_chessse;
                                                    $descuento_paquete = $totalPrecioLista - $precio_paquete;
                                                    //si es superbotana con extraqueso ya trae el precio incluido
                                                    // $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][]['id_porcion']=0; 
                                                    $precio_producto = $precio_producto + $precio_extra_chessse;
                                                    $precio_dm_paquete = $precio_producto - $descuento_paquete;
                                                    /* CALCULAR IVA */
                                                    if (Store::getStatusTaxStore($data) > 0):
                                                        //calculos para el cobro del iva..
                                                        $cant_sin_iva = ($precio_dm_paquete / 1.16);
                                                        $cant_iva_round = ($cant_sin_iva * 100) / 100;
                                                        //desgloce..
                                                        $iva_float = $cant_iva_round * 0.16;
                                                        $iva = ($iva_float * 100) / 100;
                                                        $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];

                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto;

                                                    else:
                                                        $iva = 0;
                                                        $impporc_producto = 0;
                                                    endif;

                                                    //aumentar 15 pesos al precio del producto
                                                   // $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $precio_producto . '.00';
                                                    //nueva
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $precio_dm_paquete . '.00';                                      
                                                    //---
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $precio_dm_paquete;
                                                    
                                                    if ($rowConfPackage->app_porcion_armado_pizza == 4) {
                                                        $id_porcion = 3;
                                                        for ($i = 0; $i <= 3; $i++) {

                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $reseta_extra_queso;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = $cont_receta_queso;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Con extraqueso';
                                                            $id_porcion++;
                                                        }
                                                        $cont_recetas_base4x4++;
                                                    } else {

                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $reseta_extra_queso;
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = $cont_receta_queso;
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Con extraqueso';

                                                        $cont_recetas_full++;
                                                    }

                                                endif;
                                               
                                                if ($arrayBuilder[0]->orilla_queso == $Constans['CON_ORILLA']) {

                                                    $dataPrice['id_ingrediente_pizza'] = $Constans['ORILLA_RELLENA_QUESO'];
                                                    $dataPrice['id_tamano_articulo'] = $rowConfPackage->id_tamano_articulo;
                                                    $arrayPrice = Product::getPriceExtrasByParams($dataPrice);

                                                    $precio_orilla_queso = $arrayPrice[0]->precio_ingrediente_tamano;
                                                    //$precio_orilla_queso = $clOnline->get_orilla_queso_price() * $cant_pedido;
                                                    //    var_dump($precio_orilla_queso);
                                                    $totalPrecioLista+=$precio_orilla_queso;
                                                    $descuento_paquete = $totalPrecioLista - $precio_paquete;
                                                    //si es superbotana con extraqueso ya trae el precio incluido
                                                    // $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][]['id_porcion']=0; 
                                                    $precio_producto = $precio_producto + $precio_orilla_queso;
                                                    $precio_dm_paquete = $precio_producto - $descuento_paquete;

                                                    if (Store::getStatusTaxStore($data) > 0):
                                                        //calculos para el cobro del iva..
                                                        $cant_sin_iva = ($precio_dm_paquete / 1.16);
                                                        $cant_iva_round = $cant_sin_iva * 100 / 100;
                                                        //desgloce..
                                                        $iva_float = $cant_iva_round * 0.16;
                                                        $iva = ($iva_float * 100) / 100;
                                                        $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];
                                                        //  var_dump($precio_dm_paquete);
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto;


                                                    else:
                                                        $iva = 0;
                                                        $impporc_producto = 0;
                                                    endif;

                                                    //aumentar 15 pesos al precio del producto
                                                   // $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $precio_producto . '.00';
                                                    //nueva
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $precio_dm_paquete . '.00';                                      
                                                    //---
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $precio_dm_paquete;

                                                    //aumentar precio orilla queso..
                                                     if ($rowConfPackage->app_porcion_armado_pizza == 4) {
                                                         $id_porcion = 3;
                                                        for ($i = 0; $i <= 3; $i++) {
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $Constans['RECETA_ORILLA_QUESO'];
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = 1;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Orilla de queso';

                                                            $id_porcion++;
                                                        }
                                                        $cont_recetas_base4x4++;
                                                    } else {

                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $Constans['RECETA_ORILLA_QUESO'];
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Orilla de queso';
                                                        $cont_recetas_full++;
                                                    }
                                                }//fin orilla

                                                //  var_dump($precio_dm_paquete);

                                                if ($arrayBuilder[0]->borde < 0):
                                                    // $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][]['id_porcion']=0; 
                                                    
                                                    if ($rowConfPackage->app_porcion_armado_pizza == 4) {
                                                        $id_porcion = 3;
                                                        for ($i = 0; $i <= 3; $i++) {
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $Constans['RECETA_AJOJOLI'];
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = -1;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Sin ajojolí';

                                                            $id_porcion++;
                                                        }
                                                        $cont_recetas_base4x4++;
                                                    } else {


                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $Constans['RECETA_AJOJOLI'];
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = -1;
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Sin ajojolí';

                                                        $cont_recetas_full++;
                                                    }

                                                endif;


                                                if ($arrayBuilder[0]->pan_cruji == $Constans['CON_PANCRUJI']):

                                                    if ($rowConfPackage->app_porcion_armado_pizza == 4) {
                                                        $id_porcion = 3;
                                                        for ($i = 0; $i <= 3; $i++) {
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $Constans['RECETA_PAN_CRUJI'];
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = 1;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Pan cruji';

                                                            $id_porcion++;
                                                        }
                                                        $cont_recetas_base4x4++;
                                                    } else {
                                                        
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $Constans['RECETA_PAN_CRUJI'];
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Pan cruji';
                                                        $cont_recetas_full++;
                                                    }
                                                endif;



                                                if ($rowConfPackage->id_articulo == $Constans['PIZZA_BASE'] || $rowConfPackage->id_articulo = $Constans['PIZZA_BASE_ESP']) {
                                                    
                                                    
                                                    $cont_recetas_left4x4 = $cont_recetas_base4x4+1; //return the last position of base recipe
                                                    $cont_recetas_middle14x4 = $cont_recetas_base4x4+1;
                                                    $cont_recetas_middle24x4 = $cont_recetas_base4x4+1;
                                                    $cont_recetas_right4x4 = $cont_recetas_base4x4+1;
                                                

                                                    if ($arrayBuilder[0]->ban_cuartos_esp > 0 || $rowConfPackage->app_porcion_armado_pizza == 4) {
                                                       // var_dump($rowConfPackage);
                                                       if ($arrayBuilder[0]->id_esp_left4x4_builder > 0) {
                                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0])) {
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 3;
                                                            }

                                                            $arrayRecipe = Product::where('id_articulo', $arrayBuilder[0]->id_esp_left4x4_builder)->get();
                                                            $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                            $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                            //$id_receta_ventamaxx = $clOnline->get_receta_vmx_by_id_articulo($clOnline->id_esp_left_builder);
                                                            // $nombre_ingrediente = $clOnline->get_nombre_articulo_by_parameter($clOnline->id_esp_left_builder);
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['id_receta'] = $id_receta_ventamaxx;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['cantidad_receta'] = 1;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['descripcion_receta'] = $nombre_ingrediente;

                                                            $cont_recetas_left4x4++;
                                                        }
                                                        
                                                        if ($arrayBuilder[0]->id_esp_middle14x4_builder > 0) {

                                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 4;
                                                            }

                                                            $arrayRecipe = Product::where('id_articulo', $arrayBuilder[0]->id_esp_middle14x4_builder)->get();
                                                            $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                            $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['id_receta'] = $id_receta_ventamaxx;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['cantidad_receta'] = 1;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['descripcion_receta'] = $nombre_ingrediente;

                                                            $cont_recetas_middle14x4++;
                                                        }
                                                        
                                                        if ($arrayBuilder[0]->id_esp_middle24x4_builder > 0) {

                                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 5;
                                                            }

                                                            $arrayRecipe = Product::where('id_articulo', $arrayBuilder[0]->id_esp_middle24x4_builder)->get();
                                                            $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                            $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['id_receta'] = $id_receta_ventamaxx;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['cantidad_receta'] = 1;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['descripcion_receta'] = $nombre_ingrediente;

                                                            $cont_recetas_middle24x4++;
                                                        }
                                                        
                                                        
                                                        if ($arrayBuilder[0]->id_esp_right4x4_builder > 0) {

                                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3])) {
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['id_porcion'] = 6;
                                                            }

                                                            $arrayRecipe = Product::where('id_articulo', $arrayBuilder[0]->id_esp_right4x4_builder)->get();
                                                            $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                            $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['id_receta'] = $id_receta_ventamaxx;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['cantidad_receta'] = 1;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['descripcion_receta'] = $nombre_ingrediente;

                                                            $cont_recetas_right4x4++;
                                                        }
                                                    }
                                                    else{
                                                       
                                                         if (($arrayBuilder[0]->ban_mitad_esp > 0) || ($arrayBuilder[0]->id_esp_left_builder>0) || ($arrayBuilder[0]->id_esp_right_builder>0)  ) {


                                                            if ($arrayBuilder[0]->id_esp_left_builder > 0):
                                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 1;
                                                                }

                                                                $arraySpeciality = Product::where('id_articulo', $arrayBuilder[0]->id_esp_left_builder)->get();
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['id_receta'] = $arraySpeciality[0]->id_art_receta_ventamaxx;
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['cantidad_receta'] = 1;
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['descripcion_receta'] = $arraySpeciality[0]->web_nombre_articulo;

                                                                $cont_recetas_left++;

                                                            endif;

                                                            if ($arrayBuilder[0]->id_esp_right_builder > 0):

                                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 2;
                                                                }


                                                                $arraySpeciality = Product::where('id_articulo', $arrayBuilder[0]->id_esp_right_builder)->get();
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['id_receta'] = $arraySpeciality[0]->id_art_receta_ventamaxx;
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['cantidad_receta'] = 1;
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['descripcion_receta'] = $arraySpeciality[0]->web_nombre_articulo;

                                                                $cont_recetas_right++;

                                                            endif;
                                                        }//fin mitades..
                                                    }//fin else cuartos...
                                                    //si no, revisar las mitades..
                                                    //obtener ingredientes..
                                                    $dataConfigPizza['id_procesado_orden_pizza_builder'] = $arrayBuilder[0]->id_procesado_orden_pizza_builder;                                                                                                     
                                                    $arrayConfigPizza = Processor::getProcesadoOrderConfigPizzabyId($dataConfigPizza);
                                                    
                                                    //   $resultConfigPizza = $clOnline->get_procesado_config_pizza_by_id($clOnline->id_procesado_orden_pizza_builder);
                                                    if (count($arrayConfigPizza) > 0) {
                                                        //$cont_recetas_full = $cont_recetas_full; //recetas
                                                        //$cont_recetas_left =0; 
                                                        //$cont_recetas_right =0; 
                                                      
                                                        foreach ($arrayConfigPizza as $rowConfigPizza) {
                                                           
                                                            $rowIngrediente = Ingredient::where('id_ingrediente_pizza', $rowConfigPizza->id_ingrediente_pizza)->get();
                                                            $nombre_ingrediente = $rowIngrediente[0]->descripcion_ingrediente_pizza;
                                                            $id_receta_ventamaxx = $rowIngrediente[0]->id_receta_ventamaxx;

                                                            if ($arrayBuilder[0]->ban_cuartos_esp > 0 | $rowConfPackage->app_porcion_armado_pizza == 4) {                                                             

                                                                if ($rowConfigPizza->porcion_ingrediente_pizza == 'left4x4') {
                                                                    //CHECAR SI EXISTE id_porcion
                                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0])) {
                                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 3;
                                                                    }

                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['id_receta'] = $id_receta_ventamaxx;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['cantidad_receta'] = $rowConfigPizza->cantidad_ingrediente_pizza;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['descripcion_receta'] = $nombre_ingrediente;

                                                                    $cont_recetas_left4x4++;
                                                                } else
                                                                if ($rowConfigPizza->porcion_ingrediente_pizza == 'middle14x4') {

                                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 4;
                                                                    }

                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['id_receta'] = $id_receta_ventamaxx;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['cantidad_receta'] = $rowConfigPizza->cantidad_ingrediente_pizza;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['descripcion_receta'] = $nombre_ingrediente;

                                                                    $cont_recetas_middle14x4++;
                                                                } else
                                                                if ($rowConfigPizza->porcion_ingrediente_pizza == 'middle24x4') {

                                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 5;
                                                                    }

                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['id_receta'] = $id_receta_ventamaxx;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['cantidad_receta'] = $rowConfigPizza->cantidad_ingrediente_pizza;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['descripcion_receta'] = $nombre_ingrediente;

                                                                    $cont_recetas_middle24x4++;
                                                                } else
                                                                if ($rowConfigPizza->porcion_ingrediente_pizza == 'right4x4') {

                                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3])) {
                                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['id_porcion'] = 6;
                                                                    }

                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['id_receta'] = $id_receta_ventamaxx;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['cantidad_receta'] = $rowConfigPizza->cantidad_ingrediente_pizza;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['descripcion_receta'] = $nombre_ingrediente;

                                                                    $cont_recetas_right4x4++;
                                                                }
                                                            } else {

                                                                // 0 = COMPLETA, 1 = LEFT, 2 =  RIGHT..
                                                                if ($rowConfigPizza->porcion_ingrediente_pizza == 'left') {
                                                                       
                                                                    //CHECAR SI EXISTE id_porcion
                                                                    // dd( $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion']);
                                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 1;
                                                                    }


                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['id_receta'] = $id_receta_ventamaxx;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['cantidad_receta'] = $rowConfigPizza->cantidad_ingrediente_pizza;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['descripcion_receta'] = $nombre_ingrediente;

                                                                    $cont_recetas_left++;
                                                                } else
                                                                if ($rowConfigPizza->porcion_ingrediente_pizza == 'right') {

                                                                    if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 2;
                                                                    }

                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['id_receta'] = $id_receta_ventamaxx;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['cantidad_receta'] = $rowConfigPizza->cantidad_ingrediente_pizza;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['descripcion_receta'] = $nombre_ingrediente;

                                                                    $cont_recetas_right++;
                                                                } else {

                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $id_receta_ventamaxx;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = $rowConfigPizza->cantidad_ingrediente_pizza;
                                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = $nombre_ingrediente;

                                                                    $cont_recetas_full++;
                                                                }
                                                            } ///fin ban mitades..
                                                        }//fin foreachj
                                                    } //fin de si tiene ingredientes.
                                                } // if pizza base, pizza base esp
                                                else { //fin de pizza base
                                                    //METO LA ESPECIALIDAD DIRECTO..
                                                    
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $clOnline->config_paquete_id_receta_ventamaxx;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = $clOnline->cantidad_articulo;

                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = $clOnline->web_nombre_articulo;
                                                    $cont_recetas_full++; //AHORA SI LA VARIABLE NORMAL
                                                }
                                            }//IF IS PIZZA OR SPECIALITY
                                            //CHECAR RECETAS EN CONFIG PIZZA...
                                            //FIN DE SI ES ESPECIALIDAD O PIZZA
                                        } else {

                                            /* if($id_articulo_procesada_orden==ID_2BPAYS_REWARDS):

                                              //echo 'precio dm'.$precio_dm_paquete;
                                              $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = 0;
                                              endif;

                                              //si es bebida , papacha o cualquier otra cosa..
                                              $cont_recetas_full = 0;
                                              if ($categoria_articulo == ENTRADAS || $categoria_articulo == POSTRES || $categoria_articulo == BAGUETTES):
                                              $receta_base = $clOnline->set_receta_base_by_id_categoria($clOnline->id_categoria);
                                              
                                              $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 0;
                                              $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $receta_base;
                                              $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                              $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Receta base';
                                              $cont_recetas_full++;
                                              endif; */
                                            
                                            $arrayRecetaBase = Category::where('id_categoria', $rowConfPackage->id_categoria)->get();
                                            if ($arrayRecetaBase[0]->receta_base_ventamaxx > 0) {
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 0;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $arrayRecetaBase[0]->receta_base_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Receta base';
                                                $cont_recetas_full++;
                                            }
                                            
                                            //SI ES UNA BEBIDA LO AGREGA DIRECTO. 
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 0;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $rowConfPackage->config_paquete_id_receta_ventamaxx;
                                            //$json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta']=$clOnline->cantidad_articulo;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = $rowConfPackage->cantidad_articulo;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = $rowConfPackage->web_nombre_articulo;
                                        }

                                        $cont_producto++; //AUMENTAR LOS PRODUCTOS QUE COMPONEN EL PAQUETE..
                                    } //fin de while
                                }
                                else
                                if ($categoryProduct[0]->id_categoria_promocion == $Constans['2X1_BASICO'] || $categoryProduct[0]->id_categoria_promocion == $Constans['2X1_ESPECIALIDAD']) {
                                   
                                    /*$cont_recetas_sub_full = 0;
                                    $cont_recetas_sub_left = 0;
                                    $cont_recetas_sub_right = 0;*/
                                    $totalPrecioLista = 0;
                                    $cant_pedido = $rowProduct->cantidad_articulo_pedido;                               

                                    $data2x1['id_procesado_articulo_pedido'] = $rowProduct->id_procesado_articulo_pedido;
                                    $arrayBasesPizzaSpeciality = Processor::getPizzaBaseOrSpecialityById($data2x1);
                                    //counting for obtain the base more spensive
                                    //SI ENCUENTRA QUIERE DECIR QUE EXISTE CONFIG POR PIZZA BUILDER..
                                    if (count($arrayBasesPizzaSpeciality) > 0):
                                                                            
                                        $data2x1['id_articulo'] = $Constans['PIZZA_BASE'];
                                        $RowsPizzaBase2ing = Processor::getPizzaBaseById($data2x1);
                                        $data2x1['id_articulo'] = $Constans['PIZZA_BASE_ESP'];
                                        $RowsPizzaEsp = Processor::getPizzaBaseById($data2x1); //ok
                                        
                                        if (count($RowsPizzaBase2ing) == 2 || count($RowsPizzaEsp) == 2):
                                            
                                            //looks for procesado_articulo_pedido                                                    
                                            // $resultConfigPaquete = $clOnline->getConfigPizzasOrderByExtrachesse($data2x1);
                                            $arrayConfigPromo2x1 = Processor::getConfigPizzasOrderByExtrachesse($data2x1);
                                            
                                           // dd($arrayConfigPromo2x1);
                                        
                                        else:
                                            //$clOnline->get_procesado_orden_config_paquete_order_by_id_articulo_desc();
                                            $arrayConfigPromo2x1 = Processor::getConfigPizzaOrderIditemsDesc($data2x1);

                                        endif;

                                    else:
                                       
                                        $arrayConfigPromo2x1 = Processor::getConfigPizzaOrderByPrice($data2x1);
                                    endif;
                                    

                                    $totalPrecioLista = Processor::getTotalOrderConfigPackageByid($data2x1) * $cant_pedido;                               
                                   // $descuento_paquete = $totalPrecioLista - $precio_paquete;

                                    //get totalPrecioLista if exist 37 ..
                                    
                                    foreach ($arrayConfigPromo2x1 as $Row){
                                        if ($Row->id_articulo == $Constans['PIZZA_BASE_ESP']) {

                                            $dataProduct['id_articulo'] = $Constans['PIZZA_BASE'];
                                            $dataProduct['id_tamano_articulo'] = $Row->id_tamano_articulo;
                                            $arrayProductTemp = Product::getProductPrice($dataProduct);
                                           // $precio_producto += $arrayProductTemp[0]->precio_articulo_tamano * $cant_pedido;
                                            //cambio el total precio lista..
                                            $totalPrecioLista+=$arrayProductTemp[0]->precio_articulo_tamano * $cant_pedido;
                                            //cambio descuento..
                                           // $descuento_paquete = $totalPrecioLista - $precio_paquete;
                                        }
                                    }
                                    //cambiar precio de pizza 2x1, està obteniendo base esp en 20 pesos
                                    //dd($totalPrecioLista);                                   
                                    $countMl = 1;

                                    foreach ($arrayConfigPromo2x1 as $RowPromo) {
                                        
                                      //  dd($RowPromo);
                                        
                                         if ($RowPromo->id_articulo == $Constans['PIZZA_BASE_ESP']) {

                                            $dataProduct['id_articulo'] = $Constans['PIZZA_BASE'];
                                            $dataProduct['id_tamano_articulo'] = $Row->id_tamano_articulo;
                                            $arrayProductTemp = Product::getProductPrice($dataProduct);
                                            $precio_articulo_tamano = ($arrayProductTemp[0]->precio_articulo_tamano)+
                                                    ($RowPromo->precio_articulo_tamano);
                                        
                                        }
                                        else{
                                            $precio_articulo_tamano = $RowPromo->precio_articulo_tamano;
                                        }
                                            
                                        

                                        //obtengo el precio real del producto del 2x1
                                        if ($countMl == 1) { //SI ES LA PRIMER PIZZA DE LA LISTA ENTONCES REALIZA EL CALCULO 
                                            //hacerle el impuesto a esta..
                                            //ESTABLECER VARIABLES DEL IVA
                                            ////////////////////////////////
                                            if (Store::getStatusTaxStore($data) > 0):
                                                //calculos para el cobro del iva..
                                                $cant_sin_iva = (($precio_articulo_tamano * $RowPromo->cantidad_articulo) / 1.16); //precio articulo.. directo de la DB
                                                $cant_iva_round = ($cant_sin_iva * 100) / 100;
                                                //desgloce..
                                                $iva_float = $cant_iva_round * 0.16;
                                                $iva = ($iva_float * 100) / 100;
                                                $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];

                                            else:
                                                $iva = 0;
                                                $impporc_producto = 0;
                                            endif;
                                            // echo 'IVA'.$iva;
                                            $precio_producto = $precio_articulo_tamano * $cant_pedido;
                                            // var_dump($precio_producto);
                                            //$porcientoImpuestoProducto = PORCIENTO_IMPUESTO_PRODUCTO;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_esquemacobro'] = 2;  //ID_ESQUEMA COBRO. COBRO GENERAL TAMBIEN
                                            $precio_dm_producto = $precio_producto;
                                        }
                                        else { //SI ES EL SEGUNDO LO MANDA EN CEROS..
                                            //todo cero aqui..
                                           
                                            $precio_producto = $precio_articulo_tamano * $cant_pedido;
                                            $precio_dm_producto = 0;
                                         
                                            $iva = 0;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_esquemacobro'] = $RowPromo->config_id_esquema_cobro_ventamaxx;
                                        }
                                        
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_producto'] = $cont_producto + 1;

                                        if($RowPromo->app_porcion_armado_pizza==4){
                                            //IF PIZZA BUILDER SIZE CANT BUILD ON 4 PORTIONS, THEN SENDS THE OTHER VENTAMAXX SIZE
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_tamano'] = $RowPromo->app_id_porciones_tamanno_ventamaxx;
                                        }
                                        else{
                                          $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_tamano'] = $RowPromo->config_paquete_id_tamanno_ventamaxx;

                                        }

                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['cantidad_producto'] = $RowPromo->cantidad_articulo;
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['comment_prodducto'] = ' ';
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['descripcion_prodducto'] = $RowPromo->web_descripcion_tamano_articulo . ' ' . $RowPromo->web_nombre_articulo;

                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $precio_producto . '.00';
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $precio_dm_producto;
                                        // $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_esquemacobro'] = $clOnline->config_id_esquema_cobro_ventamaxx; //ocupo el esquema cobro de cada paquete..
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['esquemacobro_producto'] = $nombre_paquete; //descripcion del paquete..
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                        $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto;

                                        ////AQUI EMPIEZA A BUSCAR LA ESPECIALIDAD O EL ARMADO DE LA PIZZA..


                                        if ($RowPromo->id_articulo == $Constans['PIZZA_BASE'] || $RowPromo->id_articulo == $Constans['PIZZA_BASE_ESP']):
                                            // $resultPktBuilder =  $clOnline->get_procesado_orden_builder_esp_config_by_id();
                                            $data2x1['id_procesado_articulo_pedido'] = $RowPromo->id_procesado_articulo_pedido;
                                            $data2x1['id_procesado_orden_pizza_builder'] = $RowPromo->id_procesado_orden_pizza_builder;
                                            $arrayPktBuilder = Processor::getConfigBuilderByParams($data2x1);
                                        //SI ES PIZZA ARMADA FORMA 2 REGISTROS..

                                        else:
                                            $data2x1['id_procesado_articulo_pedido'] = $RowPromo->id_procesado_articulo_pedido;
                                            $arrayPktBuilder = Processor::getConfigBuilderByItemId($data2x1);
                                        ///JUST ONE BUILDER RECORD
                                        //SI ES PIZZA ARMADA FORMA 2 REGISTROS..
                                        endif;

                                        $reseta_base = ($arrayPktBuilder[0]->salsa == 1) ? $Constans['RECETA_BARBECUE'] : $Constans['RECETA_SAPRE'];
                                        
                                        //inicializo receta base 4x4
                                        $cont_recetas_base4x4=0;
                                        
                                        if ($RowPromo->app_porcion_armado_pizza == 4) {
                                            $id_porcion = 3;
                                            for ($i = 0; $i <= 3; $i++) {

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $reseta_base;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'SALSA';
                                                $id_porcion++;
                                            }
                                            $cont_recetas_base4x4++;
                                        } else {
                                            //RESETA BASE..
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 0;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $reseta_base;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'SALSA';

                                            $cont_recetas_full++;
                                        }


                                        if ($arrayPktBuilder[0]->extra_chesse == 1): //extraquese
                                            if ($countMl == 1) { //SI ES LA PIZZA MAS CARA..
                                                $dataPrice['id_ingrediente_pizza'] = $Constans['EXTRA_CHESSE'];
                                                $dataPrice['id_tamano_articulo'] = $RowPromo->id_tamano_articulo;
                                                //$arrayPrice = Product::getProductPrice($dataPrice);
                                                $arrayPrice = Product::getPriceExtrasByParams($dataPrice);
                                                $precio_extra_chessse = $arrayPrice[0]->precio_ingrediente_tamano;
                                                $precio_extra_chessse = $precio_extra_chessse * $cant_pedido;
                                 
                                                $precio_producto = $precio_producto + $precio_extra_chessse;
                                                $precio_dm_producto = $precio_producto;
                                                
                                                if (Store::getStatusTaxStore($data) > 0):
                                                    //calculos para el cobro del iva..
                                                    $cant_sin_iva = ($precio_producto / 1.16);
                                                    $cant_iva_round = ($cant_sin_iva * 100) / 100;
                                                    //desgloce..
                                                    $iva_float = $cant_iva_round * 0.16;
                                                    $iva = ($iva_float * 100) / 100;
                                                    $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];

                                                else:
                                                    $iva = 0;
                                                    $impporc_producto = 0;
                                                endif;
                                                $porcientoImpuestoProducto = $impporc_producto;
                                                //recalcular iva por que lleva extraqueso...
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                            }
                                            else {
                                                // $precio_producto=0; 
                                                //SI ES LA 2DA PIZZA COBRO EL EXTRAQUESO Y PRECIO NORMAL PERO SIN IMPUESTOS.
                                                $dataPrice['id_ingrediente_pizza'] = $Constans['EXTRA_CHESSE'];
                                                $dataPrice['id_tamano_articulo'] = $RowPromo->id_tamano_articulo;
                                                //$arrayPrice = Product::getProductPrice($dataPrice);
                                                $arrayPrice = Product::getPriceExtrasByParams($dataPrice);
                                                $precio_extra_chessse = $arrayPrice[0]->precio_ingrediente_tamano;
                                                $precio_extra_chessse = $precio_extra_chessse * $cant_pedido;
                                                $precio_producto = $precio_producto + $precio_extra_chessse;
                                                $precio_dm_producto = 0;
                                              
                                            }

                                            //aumentar 15 pesos al precio del producto
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $precio_producto . '.00';
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $precio_dm_producto;

                                            if ($RowPromo->app_porcion_armado_pizza == 4) {
                                                $id_porcion = 3;
                                                for ($i = 0; $i <= 3; $i++) {

                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $Constans['RECETA_QUESO'];
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = 1;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Con extraqueso';
                                                    $id_porcion++;
                                                }
                                                $cont_recetas_base4x4++;
                                            } else {

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $Constans['RECETA_QUESO'];
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Con extraqueso';
                                                $cont_recetas_full++;
                                            }

                                        endif; //fin extraqueso..

                                        if ($arrayPktBuilder[0]->borde < 0):
                                            if ($RowPromo->app_porcion_armado_pizza == 4) {
                                                $id_porcion = 3;
                                                for ($i = 0; $i <= 3; $i++) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $Constans['RECETA_AJOJOLI'];
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = -1;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Sin ajojolí';

                                                    $id_porcion++;
                                                }
                                                $cont_recetas_base4x4++;
                                            } else {

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $Constans['RECETA_AJOJOLI'];
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = -1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Sin ajojolí';

                                                $cont_recetas_full++;
                                            }

                                        endif;

                                        if ($arrayPktBuilder[0]->orilla_queso == $Constans['CON_ORILLA']):
                                            
                                            if ($countMl == 1) {
                                                $dataPrice['id_ingrediente_pizza'] = $Constans['ORILLA_RELLENA_QUESO'];
                                                $dataPrice['id_tamano_articulo'] = $RowPromo->id_tamano_articulo;
                                                $arrayPrice = Product::getPriceExtrasByParams($dataPrice);
                                                $precio_orilla_queso = $arrayPrice[0]->precio_ingrediente_tamano;
                                                $totalPrecioLista+=$precio_orilla_queso;
                                                $descuento_paquete = $totalPrecioLista - $precio_paquete;
                                                //si es superbotana con extraqueso ya trae el precio incluido
                                                // $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][]['id_porcion']=0; 
                                                $precio_producto = $precio_producto + $precio_orilla_queso;
                                                $precio_dm_producto = $precio_producto;

                                                if (Store::getStatusTaxStore($data) > 0):
                                                    //calculos para el cobro del iva..
                                                    $cant_sin_iva = ($precio_producto / 1.16);
                                                    $cant_iva_round = $cant_sin_iva * 100 / 100;
                                                    //desgloce..
                                                    $iva_float = $cant_iva_round * 0.16;
                                                    $iva = ($iva_float * 100) / 100;
                                                    $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];
                                                    //  var_dump($precio_dm_paquete);
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto;


                                                else:
                                                    $iva = 0;
                                                    $impporc_producto = 0;
                                                endif;
                                            }
                                            else {
                                                //SI ES LA 2DA PIZZA COBRO LA ORILLA Y PRECIO NORMAL PERO SIN IMPUESTOS.
                                                $dataPrice['id_ingrediente_pizza'] = $Constans['ORILLA_RELLENA_QUESO'];
                                                $dataPrice['id_tamano_articulo'] = $RowPromo->id_tamano_articulo;
                                                //$arrayPrice = Product::getProductPrice($dataPrice);
                                                $arrayPrice = Product::getPriceExtrasByParams($dataPrice);
                                                $precio_orilla_queso = $arrayPrice[0]->precio_ingrediente_tamano;
                                                $precio_orilla_queso = $precio_orilla_queso * $cant_pedido;
                                                $precio_producto = $precio_producto + $precio_orilla_queso;
                                                $precio_dm_producto = 0;
                                            }

                                            //aumentar 15 pesos al precio del producto
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $precio_producto . '.00';
                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $precio_dm_producto;

                                            if ($RowPromo->app_porcion_armado_pizza == 4) {
                                                $id_porcion = 3;
                                                for ($i = 0; $i <= 3; $i++) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $Constans['RECETA_ORILLA_QUESO'];
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = 1;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Orilla de queso';

                                                    $id_porcion++;
                                                }
                                                $cont_recetas_base4x4++;
                                            } else {

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $Constans['RECETA_ORILLA_QUESO'];
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Orilla de queso';
                                                $cont_recetas_full++;
                                            }

                                        endif; //fin orilla de queso..

                                        if ($arrayPktBuilder[0]->pan_cruji == $Constans['CON_PANCRUJI']):
                                            
                                            if ($RowPromo->app_porcion_armado_pizza == 4) {
                                                $id_porcion = 3;
                                                for ($i = 0; $i <= 3; $i++) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $Constans['RECETA_PAN_CRUJI'];
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = 1;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = 'Pan cruji';

                                                    $id_porcion++;
                                                }
                                                $cont_recetas_base4x4++;
                                            } else {

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $Constans['RECETA_PAN_CRUJI'];
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Pan cruji';
                                                $cont_recetas_full++;
                                            }

                                        endif;

                                        if ($arrayPktBuilder[0]->ban_mitad_esp > 0) {
                                            if ($arrayPktBuilder[0]->id_esp_left_builder > 0) {
                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 1;
                                                }

                                                $arrayRecipe = Product::where('id_articulo', $arrayPktBuilder[0]->id_esp_left_builder)->get();
                                                $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['id_receta'] = $id_receta_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['descripcion_receta'] = $nombre_ingrediente;

                                                $cont_recetas_left++;
                                            }

                                            if ($arrayPktBuilder[0]->id_esp_right_builder > 0) {

                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 2;
                                                }

                                                $arrayRecipe = Product::where('id_articulo', $arrayPktBuilder[0]->id_esp_right_builder)->get();
                                                $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['id_receta'] = $id_receta_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['descripcion_receta'] = $nombre_ingrediente;

                                                $cont_recetas_right++;
                                            }
                                        } //fin de si ban >1    
                                        else
                                        if ($arrayPktBuilder[0]->ban_cuartos_esp > 0 || $RowPromo->app_porcion_armado_pizza == 4) {
                                            
                                            $cont_recetas_left4x4 = $cont_recetas_base4x4+1; //return the last position of base recipe
                                            $cont_recetas_middle14x4 = $cont_recetas_base4x4+1;
                                            $cont_recetas_middle24x4 = $cont_recetas_base4x4+1;
                                            $cont_recetas_right4x4 = $cont_recetas_base4x4+1;
                            
                                            if ($arrayPktBuilder[0]->id_esp_left4x4_builder > 0) {
                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0])) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 3;
                                                }

                                                $arrayRecipe = Product::where('id_articulo', $arrayPktBuilder[0]->id_esp_left4x4_builder)->get();
                                                $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['id_receta'] = $id_receta_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['descripcion_receta'] = $nombre_ingrediente;

                                                $cont_recetas_left4x4++;
                                            }

                                            if ($arrayPktBuilder[0]->id_esp_middle14x4_builder > 0) {

                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 4;
                                                }

                                                $arrayRecipe = Product::where('id_articulo', $arrayPktBuilder[0]->id_esp_middle14x4_builder)->get();
                                                $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['id_receta'] = $id_receta_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['descripcion_receta'] = $nombre_ingrediente;

                                                $cont_recetas_middle14x4++;
                                            }
                                            
                                            if ($arrayPktBuilder[0]->id_esp_middle24x4_builder > 0) {

                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 5;
                                                }

                                                $arrayRecipe = Product::where('id_articulo', $arrayPktBuilder[0]->id_esp_middle24x4_builder)->get();
                                                $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['id_receta'] = $id_receta_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['descripcion_receta'] = $nombre_ingrediente;

                                                $cont_recetas_middle24x4++;
                                            }
                                            
                                            if ($arrayPktBuilder[0]->id_esp_right4x4_builder > 0) {

                                                if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3])) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['id_porcion'] = 6;
                                                }

                                                $arrayRecipe = Product::where('id_articulo', $arrayPktBuilder[0]->id_esp_right4x4_builder)->get();
                                                $id_receta_ventamaxx = $arrayRecipe[0]->id_art_receta_ventamaxx;
                                                $nombre_ingrediente = $arrayRecipe[0]->web_nombre_articulo;

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['id_receta'] = $id_receta_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['cantidad_receta'] = 1;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right4x4]['descripcion_receta'] = $nombre_ingrediente;

                                                $cont_recetas_right4x4++;
                                            }
                                            
                                            
                                        } //fin de si ban >1    
                                        //4x4..
                  
                                        //$Constans['PIZZA_BASE']||$RowPromo->id_articulo==$Constans['PIZZA_BASE_ESP']
                                        if (($RowPromo->id_articulo == $Constans['PIZZA_BASE'] || $RowPromo->id_articulo == $Constans['PIZZA_BASE_ESP']) ) {
                                            //obtener ingredientes..
                                            //echo '<BR>pizza buiilder'.$clOnline->id_procesado_orden_pizza_builder;
                                            $dataConfigPizza['id_procesado_orden_pizza_builder'] = $arrayPktBuilder[0]->id_procesado_orden_pizza_builder;
                                            $arrayConfigPizza = Processor::getProcesadoOrderConfigPizzabyId($dataConfigPizza);
                                            // $resultConfigPizza = $clOnline->get_procesado_config_pizza_by_id($clOnline->id_procesado_orden_pizza_builder);
                                            if (count($arrayConfigPizza) > 0) {

                                                foreach ($arrayConfigPizza as $RowConfigItem) {

                                                    //IF NOT EXIST 4X4 BUILDING
                                                    $rowIngrediente = Ingredient::where('id_ingrediente_pizza', $RowConfigItem->id_ingrediente_pizza)->get();
                                                    $nombre_ingrediente = $rowIngrediente[0]->descripcion_ingrediente_pizza;
                                                    $id_receta_ventamaxx = $rowIngrediente[0]->id_receta_ventamaxx;
                                                    //dd($arrayPktBuilder);
                                                    if ($arrayPktBuilder[0]->ban_cuartos_esp > 0 || $RowPromo->app_porcion_armado_pizza == 4) {

                                                        if ($RowConfigItem->porcion_ingrediente_pizza == 'left4x4') {
                                                            //CHECAR SI EXISTE id_porcion
                                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0])) {
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 3;
                                                            }

                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['id_receta'] = $id_receta_ventamaxx;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['cantidad_receta'] = $RowConfigItem->cantidad_ingrediente_pizza;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_left4x4]['descripcion_receta'] = $nombre_ingrediente;

                                                            $cont_recetas_left4x4++;
                                                        } else
                                                        if ($RowConfigItem->porcion_ingrediente_pizza == 'middle14x4') {

                                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 4;
                                                            }

                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['id_receta'] = $id_receta_ventamaxx;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['cantidad_receta'] = $RowConfigItem->cantidad_ingrediente_pizza;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_middle14x4]['descripcion_receta'] = $nombre_ingrediente;

                                                            $cont_recetas_middle14x4++;
                                                        } else
                                                        if ($RowConfigItem->porcion_ingrediente_pizza == 'middle24x4') {

                                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 5;
                                                            }

                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['id_receta'] = $id_receta_ventamaxx;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['cantidad_receta'] = $RowConfigItem->cantidad_ingrediente_pizza;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_middle24x4]['descripcion_receta'] = $nombre_ingrediente;

                                                            $cont_recetas_middle24x4++;
                                                        } else
                                                        if ($RowConfigItem->porcion_ingrediente_pizza == 'right4x4') {

                                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3])) {
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['id_porcion'] = 6;
                                                            }

                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right]['id_receta'] = $id_receta_ventamaxx;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right]['cantidad_receta'] = $RowConfigItem->cantidad_ingrediente_pizza;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][3]['recetas'][$cont_recetas_right]['descripcion_receta'] = $nombre_ingrediente;

                                                            $cont_recetas_right4x4++;
                                                        }
                                                    } else {

                                                        //ACOMODAR EN LA PORCION.
                                                        // 0 = COMPLETA, 1 = LEFT, 2 =  RIGHT..
                                                        if ($RowConfigItem->porcion_ingrediente_pizza == 'left') {

                                                            //CHECAR SI EXISTE id_porcion
                                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1])) {
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['id_porcion'] = 1;
                                                            }

                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['id_receta'] = $id_receta_ventamaxx;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['cantidad_receta'] = $RowConfigItem->cantidad_ingrediente_pizza;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][1]['recetas'][$cont_recetas_left]['descripcion_receta'] = $nombre_ingrediente;

                                                            $cont_recetas_left++;
                                                        } else
                                                        if ($RowConfigItem->porcion_ingrediente_pizza == 'right') {

                                                            if (!@array_key_exists('id_porcion', $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2])) {
                                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['id_porcion'] = 2;
                                                            }

                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['id_receta'] = $id_receta_ventamaxx;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['cantidad_receta'] = $RowConfigItem->cantidad_ingrediente_pizza;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][2]['recetas'][$cont_recetas_right]['descripcion_receta'] = $nombre_ingrediente;

                                                            $cont_recetas_right++;
                                                        } else {


                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $id_receta_ventamaxx;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = $RowConfigItem->cantidad_ingrediente_pizza;
                                                            $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = $nombre_ingrediente;

                                                            $cont_recetas_full++;
                                                        }
                                                    }
                                                }//endforeach..
                                            } //fin de si tiene ingredientes.
                                        } else { //fin de pizza base
                                            //METO LA ESPECIALIDAD DIRECTO..
                                            if ($arrayPktBuilder[0]->ban_cuartos_esp > 0 || $RowPromo->app_porcion_armado_pizza == 4) {
                                                //codigo pendiente..
                                                $id_porcion = 3;
                                                for ($i = 0; $i <= 3; $i++) {

                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['id_porcion'] = $id_porcion;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['id_receta'] = $RowPromo->config_paquete_id_receta_ventamaxx;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['cantidad_receta'] = $RowPromo->cantidad_articulo;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][$i]['recetas'][$cont_recetas_base4x4]['descripcion_receta'] = $RowPromo->web_nombre_articulo;
                                                    $id_porcion++;
                                                }
                                                $cont_recetas_base4x4++;
                                            } else {

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $RowPromo->config_paquete_id_receta_ventamaxx;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = $RowPromo->cantidad_articulo;

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = $RowPromo->web_nombre_articulo;
                                                $cont_recetas_full++; //AHORA SI LA VARIABLE NORMAL
                                            }
                                        }

                                        //FIN DEL ARMADO DE 2X1
                                        $cont_producto++;

                                        $countMl++;
                                        //echo "<br>countMl ".$countMl.' =>IVA< '.$iva;
                                    }
                                }//END 2X1..ELSE ANOTHER PACKAGE
                                else
                                    if ($categoryProduct[0]->id_categoria_promocion == $Constans['DESCUENTO_DIVIDIDO_ENTRE_PRODUCTOS']) {

                                        
                                        $cont_recetas_sub_full = 0;
                                        $cont_recetas_sub_left = 0;
                                        $cont_recetas_sub_right = 0;
                                        $cont_receta_queso = 1;
                                        //variables..
                                        $cont_recetas_full = 0;
                                        $cont_recetas_left = 0;
                                        $cont_recetas_right = 0;
                                        $id_paquete_aux = 0;
                                        /* 4x4 */
                                        $cont_recetas_left4x4 = 0;
                                        $cont_recetas_middle14x4 = 0;
                                        $cont_recetas_middle24x4 = 0;
                                        $cont_recetas_right4x4 = 0;
                                        $cont_recetas_base4x4 = 0;

                                        $totalPrecioLista = 0;
                                        $cant_pedido = $rowProduct->cantidad_articulo_pedido;
                                        //  $resultConfigPaquete = $clOnline->get_procesado_procesado_orden_config_paquete_by_id();
                                        $dataPackage['id_procesado_articulo_pedido'] = $rowProduct->id_procesado_articulo_pedido;
                                        $arrayConfigPackage = Processor::getOrderConfigPackageById($dataPackage);
                                        if (count($arrayConfigPackage) > 0) {
                                        ////@ $totalPrecioLista = $clOnline->get_total_precio_orden_config_paquete_by_id() * $cant_pedido;
                                        // $totalPrecioLista = $clOnline->get_total_precio_orden_config_paquete_by_id();
                                        $totalPrecioLista = (Processor::getTotalOrderConfigPackageByid($dataPackage) * $cant_pedido);
                                        
                                        if($totalPrecioLista>0){
                                            
                                             $totalPrecioLista = $totalPrecioLista / count($arrayConfigPackage);
                                        //DIVIDE TOTAL / TOTAL PRODUCT ITEMS

                                        $descuento_paquete = $totalPrecioLista - ($precio_paquete / count($arrayConfigPackage));
                                        //dd($descuento_paquete);
                                        // while ($arrayConfigPaquete = mysql_fetch_array($resultConfigPaquete)) {
                                        foreach ($arrayConfigPackage as $rowConfPackage) {

                                                $categoryItem = Category::where('id_categoria', $rowConfPackage->id_categoria)->get();
                                                $dataProduct['id_articulo'] = $rowConfPackage->id_articulo;
                                                $dataProduct['id_tamano_articulo'] = $rowConfPackage->id_tamano_articulo;
                                                $arrayProduct = Product::getProductPrice($dataProduct);
                                                $precio_producto = $arrayProduct[0]->precio_articulo_tamano * $cant_pedido;
                                                // dd($descuento_paquete);
                                                //VALIDATE IF DISCOUNT IS ACTIVE FOR CATEGORY ITEM 
                                                if ($categoryItem[0]->ban_descuento_paquete_categoria == 1) {

                                                    $precio_dm_paquete = $precio_producto - $descuento_paquete;
                                                } else {
                                                    //MULTIPLICAR POR LA CANTIDAD DEL ARTICULO POR QUE EN VENTAMAXX NO CUADRABA EL PRECIO.. 
                                                    //@$precio_producto = ($clOnline->get_price_by_id_article() * $cant_pedido)*$clOnline->cantidad_articulo;
                                                    $precio_producto = $precio_producto * $rowConfPackage->cantidad_articulo; //lo mando solo multiplicado por su cantidad..
                                                    //$precio_dm_paquete = $precio_producto-$descuento_paquete;

                                                    $precio_dm_paquete = $precio_producto;
                                                }
                                                //echo 'precio producto'.$precio_producto;
                                                //$precio_dm_paquete = $precio_producto-$descuento_paquete;
                                                //$cant_sin_iva = ($precio_producto/ 1.16);
                                                if (Store::getStatusTaxStore($data) > 0 || $rowConfPackage->id_categoria == $Constans['BEBIDAS']):
                                                    //calculos para el cobro del iva..
                                                    $cant_sin_iva = ($precio_dm_paquete / 1.16);
                                                    $cant_iva_round = ($cant_sin_iva * 100) / 100;
                                                    //desgloce..
                                                    $iva_float = $cant_iva_round * 0.16;
                                                    $iva = ($iva_float * 100) / 100;
                                                    $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];

                                                else:
                                                    $iva = 0;
                                                    $impporc_producto = 0;
                                                endif;
                                                //  dd($rowConfPackage);
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_producto'] = $cont_producto + 1;

                                                if ($rowConfPackage->app_porcion_armado_pizza == 4) {
                                                    //IF PIZZA BUILDER SIZE CANT BUILD ON 4 PORTIONS, THEN SENDS THE OTHER VENTAMAXX SIZE
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_tamano'] = $rowConfPackage->app_id_porciones_tamanno_ventamaxx;
                                                } else {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_tamano'] = $rowConfPackage->config_paquete_id_tamanno_ventamaxx;
                                                }

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['cantidad_producto'] = $cant_pedido*$rowConfPackage->cantidad_articulo;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['comment_prodducto'] = ' ';
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['descripcion_prodducto'] = $rowConfPackage->web_descripcion_tamano_articulo . ' ' . $rowConfPackage->web_nombre_articulo;

                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $precio_producto . '.00';
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $precio_dm_paquete;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_esquemacobro'] = $rowConfPackage->config_id_esquema_cobro_ventamaxx; //ocupo el esquema cobro de cada paquete..
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['esquemacobro_producto'] = $nombre_paquete; //descripcion del paquete..
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto;


                                                $arrayRecetaBase = Category::where('id_categoria', $rowConfPackage->id_categoria)->get();

                                                if ($arrayRecetaBase[0]->receta_base_ventamaxx > 0) {
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 0;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $arrayRecetaBase[0]->receta_base_ventamaxx;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Receta base';
                                                    $cont_recetas_full++;
                                                }

                                                //SI ES UNA BEBIDA LO AGREGA DIRECTO. 
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 0;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $rowConfPackage->config_paquete_id_receta_ventamaxx;
                                                //$json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta']=$clOnline->cantidad_articulo;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = $rowConfPackage->cantidad_articulo;
                                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = $rowConfPackage->web_nombre_articulo;

                                                $cont_producto++;
                                            } //FIN FOREACH..
                                        }
                                    }
                                    
                                }
                            }//fin de paquetes..   
                            else{
                              
                                 //IF IT'S NOT PROMOTION, NO PIZZA. AND CONTANINS ONLY ONE RECIPE   
                                if (Store::getStatusTaxStore($data) > 0 || $categoryProduct[0]->id_categoria ==$Constans['BEBIDAS']):
                                    //calculos para el cobro del iva..
                                    $cant_sin_iva = ($rowProduct->precio_articulo_pedido / 1.16);
                                    $cant_iva_round = ($cant_sin_iva * 100) / 100;
                                    //desgloce..
                                    $iva_float = $cant_iva_round * 0.16;
                                    $iva = ($iva_float * 100) / 100;
                                    $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];

                                else:
                                    $iva = 0;
                                    $impporc_producto = 0;
                                endif;
                                
                               
                                if ($rowProduct->ban_aplica_codigo_promocion_orden_pedido == $Constans['VIGENTE']) {
                                    //aca,, buscar articulo del cupon y calcular descuento 
                                    // var_dump($clOnline->id_tamano_articulo);

                                    $dataCode['id_procesado_articulo_pedido'] = $rowProduct->id_procesado_articulo_pedido;
                                    $dataCode['id_articulo'] = $rowProduct->id_articulo;
                                    $dataCode['id_tamano_articulo'] = $rowProduct->id_tamano_articulo;

                                    $arrayDiscount = Processor::getInfoDiscountCodeById($data);
                                    //poner todos los valores inicializados..
                                    /**/
                                    $precio_paquete = $rowProduct->precio_articulo_pedido;
                                    $precio_dm_paquete = $rowProduct->precio_articulo_pedido;
                                    $descripcion_esquemacobro = "cobro general";
                                    $id_esquemacobro = 1;

                                    if (count($arrayDiscount) > 0) {
                                        //recalcular el iva y precio_producto....
                                        //$precio_promocion_articulo=$clOnline->precio_articulo_pedido;
                                        $precio_paquete = $rowProduct->precio_articulo_pedido - $arrayDiscount->monto_descuento_procesado_orden_codigo_promocion;
                                        $descuento_paquete = $rowProduct->precio_articulo_pedido - $precio_paquete;
                                        $precio_producto = $rowProduct->precio_articulo_pedido;
                                        $precio_dm_paquete = $precio_producto - $descuento_paquete;
                                        $descripcion_esquemacobro = "DESCUENTO PEDIDO EN LINEA";
                                        $id_esquemacobro = $Constans['ESQUEMACOBRO_DESCUENTO_PEDIDO_EN_LINEA'];
                                        /* CALCULO DE IVAS */
                                        //*RECALCULO TODO*/
                                        if (Store::getStatusTaxStore($data) > 0 || $categoryProduct->id_categoria == $Constans['BEBIDAS']):
                                            //calculos para el cobro del iva..
                                            //IVA DESGLOZADO..
                                            $cant_sin_iva = ($precio_paquete / 1.16);
                                            // $cant_iva_round = round($cant_sin_iva * 100) / 100;
                                            $cant_iva_round = $cant_sin_iva * 100 / 100;
                                            //desgloce..
                                            $iva_float = $cant_iva_round * 0.16;
                                            // $iva = round($iva_float * 100) / 100;
                                            $iva = ($iva_float * 100) / 100;
                                            $impporc_producto = $Constans['PORCIENTO_IMPUESTO_PRODUCTO'];

                                        else:
                                            $iva = 0;
                                            $impporc_producto = 0;
                                        endif;
                                    }
                                }
                                else {
                                    $precio_paquete = $rowProduct->precio_articulo_pedido;
                                    $precio_dm_paquete = $rowProduct->precio_articulo_pedido;
                                    $descripcion_esquemacobro = "cobro general";
                                    $id_esquemacobro = 1;
                                    $precio_dm_paquete = $rowProduct->precio_articulo_pedido;
                                }


                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_producto'] = $cont_producto + 1;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_tamano'] = $rowProduct->procesado_id_tamanno_ventamaxx;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['cantidad_producto'] = $rowProduct->cantidad_articulo_pedido;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['comment_prodducto'] = '';
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['descripcion_prodducto'] = $rowProduct->web_descripcion_tamano_articulo . ' ' . $rowProduct->web_nombre_articulo;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['precio_producto'] = $rowProduct->precio_articulo_pedido . '.00';
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['preciodm_producto'] = $precio_dm_paquete;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['id_esquemacobro'] = $id_esquemacobro; //ocupo el esquema cobro de cada paquete..
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['esquemacobro_producto'] = $descripcion_esquemacobro; //descripcion del paquete..
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impuesto_producto'] = $iva;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['impporc_producto'] = $impporc_producto;
                                //id porcion...
                                //GET BASE FOR SOME PRODUCTS..
                                $arrayRecetaBase = Category::where('id_categoria', $categoryProduct[0]->id_categoria)->get();
                              
                                if ($arrayRecetaBase[0]->receta_base_ventamaxx > 0) {
                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 0;
                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $arrayRecetaBase[0]->receta_base_ventamaxx;
                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                    $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = 'Receta base';
                                    $cont_recetas_full++;
                                }

                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['id_porcion'] = 0;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['id_receta'] = $rowProduct->procesado_id_receta_ventamaxx;
                                // $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta']=$clOnline->cantidad_articulo_pedido;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['cantidad_receta'] = 1;
                                $json_arr['orden'][$cont_orden]['productos'][$cont_producto]['porcion'][0]['recetas'][$cont_recetas_full]['descripcion_receta'] = $rowProduct->nombre_articulo;
                            }
                             
                            //category for identify the product
                              $cont_producto++;                           
                        }//enforeachProducts..
                    }
                    $cont_orden++;
                }//endforeach orders..
              // echo "<pre>";print_r($json_arr);echo '</pre>';  
             echo json_encode($json_arr);    
            } 
            else {
                echo "null";
            }
            
        } //FIN ID UBE
        else{
            echo 'nada..';
        }
    }
    
    public function getAddressClientDeliver($arr_order) {
        $Constans = Config::get('constants.options');
        $telefono = 0;
        $dataAddress=array();
        //dd($arr_order);
        if($arr_order['id_tipo_orden']==$Constans['ID_MOSTRADOR'] ){
            // echo strlen($arr_order['celular_cliente']);
             $dataAddress['domicilio_cliente'] = $arr_order['domicilio_cliente'];
             $dataAddress['numero_ext_cliente'] = $arr_order['numero_ext_cliente'];
             $dataAddress['numero_int_cliente'] = $arr_order['numero_int_cliente'];
            // $dataAddress['numero_ext_cliente'] = $arr_order['numero_ext_cliente'];
            // $dataAddress['numero_int_cliente'] = $arr_order['numero_int_cliente'];
             $dataAddress['colonia_garantia_ube']=$arr_order['colonia_garantia_ube'];
             $dataAddress['app_colonias_garantia_ube']=$arr_order['app_colonias_garantia_ube'];
             $dataAddress['cp_cliente'] =$arr_order['cp_cliente'];
             $dataAddress['cp_garantia_ube'] =$arr_order['cp_cliente'];
             $dataAddress['referencia_domicilio_cliente'] = $arr_order['referencia_domicilio_cliente'];
             $dataAddress['msgGarantia']='';
             $dataAddress['id_telefono'] = (strlen($arr_order['celular_cliente'])>1)?$arr_order['celular_cliente']:'1';
             $dataAddress['id_tipotelefono'] =(strlen($arr_order['celular_cliente'])>1)?$arr_order['celular_cliente']:'1';
        
             
        }
        else{
            if ($arr_order['id_rel_domicilio_cliente'] > 0) {
                $dataLocation['id_cliente'] = $arr_order['id_cliente'];
                $dataLocation['id_rel_domicilio_cliente'] = $arr_order['id_rel_domicilio_cliente'];
                
                $arr_address=Client::getNoAddressById($dataLocation);
                if($arr_address[0]->id_colonias_garantia_ube>0){
                    
                    $arr_cliente = Client::getAddressById($dataLocation);
                    $dataAddress['domicilio_cliente'] = trim($arr_cliente[0]->domicilio_cliente);
                    $dataAddress['numero_ext_cliente'] = $arr_cliente[0]->numero_ext_cliente;
                    $dataAddress['numero_int_cliente'] = trim($arr_cliente[0]->numero_int_cliente);
                    $dataAddress['colonia_garantia_ube'] = $arr_cliente[0]->colonia_garantia_ube;
                    $dataAddress['app_colonias_garantia_ube'] = $arr_cliente[0]->app_colonias_garantia_ube;
                    $dataAddress['cp_garantia_ube'] = trim($arr_cliente[0]->cp_garantia_ube);
                    $dataAddress['referencia_domicilio_cliente'] = trim($arr_cliente[0]->referencia_domicilio_cliente);
                    $dataAddress['msgGarantia'] = ($arr_cliente[0]->id_status_garantia_ube == $Constans['STATUS_CON_GARANTIA']) ? 'T30' : '';

                    if (strlen(trim($arr_cliente[0]->celular_cliente)) > 0) {
                        $telefono = $arr_cliente[0]->celular_cliente;
                    } else {
                        $telefono = $arr_cliente[0]->lada_cliente . $arr_cliente[0]->telefono_cliente;
                    }

                    $dataAddress['id_telefono'] = $telefono;
                    $dataAddress['id_tipotelefono'] = $telefono;
                }
                else{
                    //si trae -1
                    $dataAddress['domicilio_cliente'] = trim($arr_address[0]->domicilio_cliente);
                    $dataAddress['numero_ext_cliente'] = $arr_address[0]->numero_ext_cliente;
                    $dataAddress['numero_int_cliente'] = trim($arr_address[0]->numero_int_cliente);
                    $dataAddress['app_colonias_garantia_ube'] = trim($arr_address[0]->app_colonias_garantia_ube);
                    $dataAddress['colonia_garantia_ube'] = (strlen($arr_address[0]->app_colonias_garantia_ube))?trim($arr_address[0]->app_colonias_garantia_ube):'N/A';
                    $dataAddress['cp_garantia_ube'] = trim($arr_address[0]->cp_cliente);
                    $dataAddress['referencia_domicilio_cliente'] = trim($arr_address[0]->referencia_domicilio_cliente);
                    $dataAddress['msgGarantia'] = '';

                    if (strlen(trim($arr_address[0]->celular_cliente)) > 0) {
                        $telefono = $arr_address[0]->celular_cliente;
                    } else {
                        $telefono = $arr_address[0]->lada_cliente . $arr_address[0]->telefono_cliente;
                    }

                    $dataAddress['id_telefono'] = $telefono;
                    $dataAddress['id_tipotelefono'] = $telefono;
                }
                
            } else {
                
                //si el domicilio es el regitro principal
                $data['id_cliente'] = $arr_order['id_cliente'];

               $arrayClient=Client::getClientNoAddressById($data);
               
               if (count($arrayClient)) {
                    //si exixte el cliente
                    if ($arrayClient[0]->id_colonias_garantia_ube > 0) { //si trae colonia
                        $arrayAddressClient = Client::getClientById($data);
                        if (count($arrayAddressClient) > 0) {

                            $dataAddress['domicilio_cliente'] = trim($arrayAddressClient[0]->domicilio_cliente);
                            $dataAddress['numero_ext_cliente'] = $arrayAddressClient[0]->numero_ext_cliente;
                            $dataAddress['numero_int_cliente'] = trim($arrayAddressClient[0]->numero_int_cliente);
                            $dataAddress['app_colonias_garantia_ube'] = trim($arrayAddressClient[0]->app_colonias_garantia_ube);
                            $dataAddress['colonia_garantia_ube'] = $arrayAddressClient[0]->colonia_garantia_ube;
                            $dataAddress['cp_garantia_ube'] = trim($arrayAddressClient[0]->cp_garantia_ube);
                            $dataAddress['referencia_domicilio_cliente'] = trim($arrayAddressClient[0]->referencia_domicilio_cliente);
                            $dataAddress['msgGarantia'] = ($arrayAddressClient[0]->id_status_garantia_ube == $Constans['STATUS_CON_GARANTIA']) ? 'T30' : '';

                            if (strlen(trim($arrayAddressClient[0]->celular_cliente)) > 0) {
                                $telefono = $arrayAddressClient[0]->celular_cliente;
                            } else {
                                $telefono = $arrayAddressClient[0]->lada_cliente . $arrayAddressClient[0]->telefono_cliente;
                            }

                            $dataAddress['id_telefono'] = $telefono;
                            $dataAddress['id_tipotelefono'] = $telefono;
                        } else {
                            $dataAddress['domicilio_cliente'] = 'N/A';
                            $dataAddress['numero_ext_cliente'] = 'N/A';
                            $dataAddress['numero_int_cliente'] = 'N/A';
                            $dataAddress['numero_ext_cliente'] = 'N/A';
                            $dataAddress['numero_int_cliente'] = 'N/A';
                            $dataAddress['colonia_garantia_ube'] = 'N/A';
                            $dataAddress['app_colonias_garantia_ube'] = '';
                            $dataAddress['cp_garantia_ube'] = '';
                            $dataAddress['referencia_domicilio_cliente'] = '';
                            $dataAddress['id_telefono'] = '';
                            $dataAddress['id_tipotelefono'] = '';
                            $dataAddress['msgGarantia'] = '';
                        }
                    } else {
                        //si trae -1 en colonia
                        $dataAddress['domicilio_cliente'] = trim($arrayClient[0]->domicilio_cliente);
                        $dataAddress['numero_ext_cliente'] = $arrayClient[0]->numero_ext_cliente;
                        $dataAddress['numero_int_cliente'] = trim($arrayClient[0]->numero_int_cliente);
                        $dataAddress['app_colonias_garantia_ube'] = trim($arrayClient[0]->app_colonias_garantia_ube);
                        $dataAddress['colonia_garantia_ube'] = trim($arrayClient[0]->app_colonias_garantia_ube);
                        $dataAddress['cp_garantia_ube'] = trim($arrayClient[0]->cp_cliente);
                        $dataAddress['referencia_domicilio_cliente'] = trim($arrayClient[0]->referencia_domicilio_cliente);
                        $dataAddress['msgGarantia'] = '';

                        if (strlen(trim($arrayClient[0]->celular_cliente)) > 0) {
                            $telefono = $arrayClient[0]->celular_cliente;
                        } else {
                            $telefono = $arrayClient[0]->lada_cliente . $arrayClient[0]->telefono_cliente;
                        }

                        $dataAddress['id_telefono'] = $telefono;
                        $dataAddress['id_tipotelefono'] = $telefono;
                    }
                }
                else{
                    //si no encuentra el cliente...
                     $dataAddress['domicilio_cliente'] = 'N/A';
                    $dataAddress['numero_ext_cliente'] = 'N/A';
                    $dataAddress['numero_int_cliente'] = 'N/A';
                    $dataAddress['numero_ext_cliente'] = 'N/A';
                    $dataAddress['numero_int_cliente'] = 'N/A';
                    $dataAddress['colonia_garantia_ube'] = 'N/A';
                    $dataAddress['cp_garantia_ube'] = '';
                    $dataAddress['referencia_domicilio_cliente'] = '';
                    $dataAddress['id_telefono'] = '';
                    $dataAddress['id_tipotelefono'] = '';
                    $dataAddress['msgGarantia'] = '';
                }
            }
        }
        
        return $dataAddress;
    }
    
    public function strip_tildes($cadena) {
        $no_permitidas = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹");
        $permitidas = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E");
        $texto = str_replace($no_permitidas, $permitidas, $cadena);
        return $texto;
    }

}
