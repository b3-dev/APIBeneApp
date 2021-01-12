<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\Store;

class StoresController extends Controller {

    //
    public function test(){
        echo date("Y-m-d H:i:s");
    }
    public function parsingArrayStores($stores) {

        $rowTmp = array();
        $arrayTmp = array();
        foreach ($stores as $row) {
         
            $rowTmp['id_unidad'] = $row->id_unidad;
            $rowTmp['nombre_unidad'] = $row->nombre_unidad;
            $rowTmp['id_zona'] = $row->id_zona;
            $rowTmp['id_estado_republica'] = $row->id_estado_republica;
            $rowTmp['id_ciudad_republica'] = $row->id_ciudad_republica;
            $rowTmp['detel_unidad']=$row->detel_unidad;
            $rowTmp['dfcdd_unidad'] = $row->dfcdd_unidad;
            $rowTmp['decll_unidad'] = $row->decll_unidad;
            $rowTmp['denum_unidad'] = $row->denum_unidad;
            $rowTmp['decol_unidad'] = $row->decol_unidad;
            $rowTmp['decpo_unidad'] = $row->decpo_unidad;
            $rowTmp['latitud_unidad'] = $row->latitud_unidad;
            $rowTmp['longitud_unidad'] = $row->longitud_unidad;
            $rowTmp['set_poligono_unidad'] = $row->set_poligono_unidad;
            $rowTmp['laminado_unidad'] = $row->laminado_unidad;
            $rowTmp['vigencia_panpizza_unidad'] = $row->vigencia_panpizza_unidad;
            $rowTmp['vigencia_unidad'] = $row->vigencia_unidad;
            $rowTmp['id_tipo_unidad'] = $row->id_tipo_unidad;
            $rowTmp['acepta_terminal_bancaria'] = $row->acepta_terminal_bancaria;
            $rowTmp['acepta_efectivo'] = $row->acepta_efectivo;
            $rowTmp['cobra_impuesto_iva'] = $row->cobra_impuesto_iva;
            $rowTmp['bussines_paypal_account'] = $row->bussines_paypal_account;
            $rowTmp['vigencia_base_deslactosado'] = $row->vigencia_base_deslactosado;
            $rowTmp['merchant_account'] = $row->merchant_account;
            $rowTmp['app_tiempo_reparto_unidad'] = $row->app_tiempo_reparto_unidad;

            $params['id_unidad'] = $row->id_unidad;
            $params['id_dia_semana'] = date("N");
            $openingHours = Store::getOpeninHourByParams($params);
            $configOpeningHours = Store::getConfigRecordOpeningHour();

            if ($configOpeningHours[0]->activa_web_config == 1) { //SI EXISTE HORARIO GENERA..
                $rowOpeningHours['id_unidad'] = $row->id_unidad;
                $rowOpeningHours['id_dia_semana'] = date("N");
                $rowOpeningHours['hora_apertura'] = $configOpeningHours[0]->hora_apertura_web_config;
                $rowOpeningHours['hora_cierre'] = $configOpeningHours[0]->hora_cierre_web_config;
            } else { //OBTENER DEL ARREGLO DE HORARIOS
                if (count($openingHours) > 0) {

                    $rowOpeningHours['id_unidad'] = $row->id_unidad;
                    $rowOpeningHours['id_dia_semana'] = date("N");
                    $rowOpeningHours['hora_apertura'] = $openingHours[0]->hora_apertura_web;
                    $rowOpeningHours['hora_cierre'] = $openingHours[0]->hora_cierre_web;
                    // $response['data']['opening_hours'] = $rowOpeningHours;
                } else { // SI NO ENCUENTRA EL RECORD, MANDAR HORARIO FIJO
                    $rowOpeningHours['id_unidad'] = $row->id_unidad;
                    $rowOpeningHours['id_dia_semana'] = date("N");
                    $rowOpeningHours['hora_apertura'] = '10:30';
                    $rowOpeningHours['hora_cierre'] = '21:30';
                    // $response['data']['opening_hours'] = $openingHours;
                }
            }

            $rowTmp['opening_hours'] = $rowOpeningHours;
            array_push($arrayTmp, $rowTmp);
        }
        return $arrayTmp;
    }
    
    public function stores(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getStores') {

                        $stores = Store::where('vigencia_unidad', 1)->orderBy('id_unidad','ASC')->get();
                        $arrayStores=$this->parsingArrayStores($stores);
                        
                        if (count($arrayStores) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $arrayStores;
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

    public function getStoreById(Request $request) {
        // echo 'aca';
        try {

            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getStoreById') {

                        $stores = Store::where('vigencia_unidad', 1)->where('id_unidad', $data['store']['id'])->get();
                        if (count($stores) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $stores;
                            $params['id_unidad'] = $data['store']['id'];
                            $params['id_dia_semana'] = date("N");
                            $openingHours = Store::getOpeninHourByParams($params);
                            $configOpeningHours = Store::getConfigRecordOpeningHour();

                            if ($configOpeningHours[0]->activa_web_config == 1) { //SI EXISTE HORARIO GENERA..
                                $rowOpeningHours['id_unidad'] = $data['store']['id'];
                                $rowOpeningHours['id_dia_semana'] = date("N");
                                $rowOpeningHours['hora_apertura'] = $configOpeningHours[0]->hora_apertura_web_config;
                                $rowOpeningHours['hora_cierre'] = $configOpeningHours[0]->hora_cierre_web_config;
                            } else { //OBTENER DEL ARREGLO DE HORARIOS
                                if (count($openingHours) > 0) {

                                    $rowOpeningHours['id_unidad'] = $data['store']['id'];
                                    $rowOpeningHours['id_dia_semana'] = date("N");
                                    $rowOpeningHours['hora_apertura'] = $openingHours[0]->hora_apertura_web;
                                    $rowOpeningHours['hora_cierre'] = $openingHours[0]->hora_cierre_web;


                                    // $response['data']['opening_hours'] = $rowOpeningHours;
                                } else { // SI NO ENCUENTRA EL RECORD, MANDAR HORARIO FIJO
                                    $rowOpeningHours['id_unidad'] = $data['store']['id'];
                                    $rowOpeningHours['id_dia_semana'] = date("N");
                                    $rowOpeningHours['hora_apertura'] = '10:30';
                                    $rowOpeningHours['hora_cierre'] = '21:30';

                                    // $response['data']['opening_hours'] = $openingHours;
                                }
                            }

                            $response['data']['opening_hours'] = $rowOpeningHours;
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
    
    public function getDSCstatus(Request $request) {
        // echo 'aca';
        try {

            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getDSCstatus') {
                        $stores = Store::where('vigencia_unidad', 1)->where('id_unidad', $data['store']['id'])->get();
                        $params['id_unidad'] = $data['store']['id'];
                        $arrayConfigDSC = Store::getDSCstatus($params);
                        $active_dsc=0;
                        if ($arrayConfigDSC[0]->activa_web_config > 0) {
                            if ($stores[0]->vigencia_dsc_unidad) {
                                $active_dsc = 1;
                            } else {
                                $active_dsc = 0;
                            }
                        } else {
                            $active_dsc = 0;
                        }

                        if (count($arrayConfigDSC) > 0) {
                            $response['status'] = 'OK';
                            $response['data']['id_unidad'] = $data['store']['id'];
                            $response['data']['active_dsc'] = $active_dsc;
                            return response()->json($response);
                        } else {
                            $response['status'] = 'OK';
                            $response['data']['id_unidad'] = $data['store']['id'];
                            $response['data']['active_dsc'] = 0;
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

}
