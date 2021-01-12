<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\Client;

class ClientsController extends Controller
{
    //
    
    
    public function parsingOthersAddress($array){
        
        $rowTmp=array();
        $arrayTmp=array();
        $arrayRowAddress = array();
        
        foreach ($array as $row) {
            if($row->id_colonias_garantia_ube>0){
                $data['id_colonias_garantia_ube'] = $row->id_colonias_garantia_ube;
                $rowRelAddress = Client::getColonyById($data);
                $arrayRowAddress=$this->parsingOtherNoAddress($rowRelAddress);               
            }
            else{
               $arrayRowAddress=$this->parsingEmptyAddress();
            }
            
            $rowTmp['id_rel_domicilio_cliente'] = $row->id_rel_domicilio_cliente;
            $rowTmp['id_cliente'] = $row->id_cliente;
            $rowTmp['id_colonias_garantia_ube'] = $row->id_colonias_garantia_ube;
            $rowTmp['app_colonias_garantia_ube'] = (strlen($row->app_colonias_garantia_ube))?$row->app_colonias_garantia_ube:'';
            $rowTmp['sucursal_id_unidad'] = ($row->sucursal_id_unidad<=0)?'':$row->sucursal_id_unidad;
            $rowTmp['numero_ext_cliente'] = $row->numero_ext_cliente;
            $rowTmp['numero_int_cliente'] = $row->numero_int_cliente;
            $rowTmp['referencia_domicilio_cliente'] = $row->referencia_domicilio_cliente;
            $rowTmp['cp_cliente'] = $row->cp_cliente;
            $rowTmp['domicilio_cliente'] = $row->domicilio_cliente;
            $rowTmp['fecha_agregado_domicilio_cliente'] = $row->fecha_agregado_domicilio_cliente;
            $rowTmp['ban_default_domicilio_cliente'] = ($row->ban_default_domicilio_cliente<=0)?0:$row->ban_default_domicilio_cliente;
            $rowTmp['lada_cliente'] = $row->lada_cliente;
            $rowTmp['telefono_cliente'] = $row->telefono_cliente;
            $rowTmp['celular_cliente'] = $row->celular_cliente;
            
            $rowTmp['colonia_garantia_ube'] = $arrayRowAddress['colonia_garantia_ube'];
            $rowTmp['descripcion_estado_republica'] = $arrayRowAddress['descripcion_estado_republica'];
            $rowTmp['descripcion_ciudad_republica'] = $arrayRowAddress['descripcion_ciudad_republica'];
            $rowTmp['latitud_domicilio_cliente'] = $row->latitud_domicilio_cliente;
            $rowTmp['longitud_domicilio_cliente'] = $row->longitud_domicilio_cliente;
            // $rowTmp['celular_cliente'] = $row->celular_cliente;
            array_push($arrayTmp, $rowTmp);
        }
        return $arrayTmp;
    }
    
    
    public function parsingOtherNoAddress($array) {
        $rowTmp = array();
        
        $rowTmp['colonia_garantia_ube'] = $array[0]->colonia_garantia_ube;
        $rowTmp['descripcion_estado_republica'] = $array[0]->descripcion_estado_republica;
        $rowTmp['descripcion_ciudad_republica'] = $array[0]->descripcion_ciudad_republica;
        
        return $rowTmp;
    }
    
    public function parsingMainAddress($row) {
        //dd($row);
        $arrayTmp=array();
        $tmpAddress = array();

        $tmpAddress['id_rel_domicilio_cliente'] = (!empty($row[0]->id_rel_domicilio_cliente))?$row[0]->id_rel_domicilio_cliente:-1;
        $tmpAddress['id_cliente'] = $row[0]->id_cliente;
        $tmpAddress['id_colonias_garantia_ube'] = $row[0]->id_colonias_garantia_ube;
        $tmpAddress['app_colonias_garantia_ube'] = (strlen($row[0]->app_colonias_garantia_ube)) ? $row[0]->app_colonias_garantia_ube : '';
        $tmpAddress['sucursal_id_unidad'] = ($row[0]->sucursal_id_unidad <= 0) ? '' : $row[0]->sucursal_id_unidad;
        $tmpAddress['numero_ext_cliente'] = $row[0]->numero_ext_cliente;
        $tmpAddress['numero_int_cliente'] = $row[0]->numero_int_cliente;
        $tmpAddress['referencia_domicilio_cliente'] = $row[0]->referencia_domicilio_cliente;
        $tmpAddress['cp_cliente'] = $row[0]->cp_cliente;
        $tmpAddress['domicilio_cliente'] = $row[0]->domicilio_cliente;
        $tmpAddress['fecha_agregado_domicilio_cliente'] = (!empty($row[0]->fecha_agregado_domicilio_cliente))?$row[0]->fecha_agregado_domicilio_cliente:'';
        $tmpAddress['ban_default_domicilio_cliente'] = ($row[0]->ban_default_domicilio_cliente <= 0) ? 0 : $row[0]->ban_default_domicilio_cliente;
        $tmpAddress['lada_cliente'] = $row[0]->lada_cliente;
        $tmpAddress['telefono_cliente'] = $row[0]->telefono_cliente;
        $tmpAddress['celular_cliente'] = $row[0]->celular_cliente;

        $tmpAddress['colonia_garantia_ube'] = (!empty($row[0]->colonia_garantia_ube))?$row[0]->colonia_garantia_ube:'';
        $tmpAddress['descripcion_estado_republica'] = (!empty($row[0]->descripcion_estado_republica))?$row[0]->descripcion_estado_republica:'';
        $tmpAddress['descripcion_ciudad_republica'] = (!empty($row[0]->descripcion_ciudad_republica))?$row[0]->descripcion_ciudad_republica:'';
        $tmpAddress['latitud_domicilio_cliente'] = $row[0]->latitud_domicilio_cliente;
        $tmpAddress['longitud_domicilio_cliente'] = $row[0]->longitud_domicilio_cliente;

        array_push($arrayTmp, $tmpAddress);
        return $arrayTmp;
    }
    
    
    

    public function parsingEmptyAddress(){
        $rowTmp = array();

        $rowTmp['colonia_garantia_ube'] = '';
        $rowTmp['descripcion_estado_republica'] = '';
        $rowTmp['descripcion_ciudad_republica'] ='';
        
        return $rowTmp;
    }
    
    public function parsingClientNoAddress($array) {
        $rowTmp = array();
        $arrayTmp = array();

        $rowTmp['id_cliente'] = $array[0]->id_cliente;
        $rowTmp['nombre_cliente'] = $array[0]->nombre_cliente;
        $rowTmp['apellido_cliente'] = $array[0]->apellido_cliente;
        $rowTmp['domicilio_cliente'] = $array[0]->domicilio_cliente;
        $rowTmp['id_colonias_garantia_ube'] = -1;
        $rowTmp['numero_ext_cliente'] = $array[0]->numero_ext_cliente;
        $rowTmp['numero_int_cliente'] = $array[0]->numero_int_cliente;
        $rowTmp['lada_cliente'] = $array[0]->lada_cliente;
        $rowTmp['telefono_cliente'] = $array[0]->telefono_cliente;
        $rowTmp['celular_cliente'] = $array[0]->celular_cliente;
        $rowTmp['cp_cliente'] = $array[0]->cp_cliente;
        $rowTmp['email_cliente'] = $array[0]->email_cliente;
        $rowTmp['passwd_cliente'] = $array[0]->passwd_cliente;
        $rowTmp['id_tipo_orden'] = $array[0]->id_tipo_orden;
        $rowTmp['fecha_cumple_cliente'] = $array[0]->fecha_cumple_cliente;
        $rowTmp['fecha_nacimiento_cliente'] = $array[0]->fecha_nacimiento_cliente;
        $rowTmp['mayoria_edad_cliente'] = $array[0]->mayoria_edad_cliente;
        $rowTmp['referencia_domicilio_cliente'] = $array[0]->referencia_domicilio_cliente;
        $rowTmp['fecha_alta_cliente'] = $array[0]->fecha_alta_cliente;
        $rowTmp['fecha_last_login'] = $array[0]->fecha_last_login;
        $rowTmp['sucursal_id_unidad'] = $array[0]->sucursal_id_unidad;
        $rowTmp['status_activa_sugerencia'] = $array[0]->status_activa_sugerencia;
        $rowTmp['ip_last_login'] = $array[0]->ip_last_login;
        $rowTmp['http_user_agent'] = $array[0]->http_user_agent;
        $rowTmp['ban_verificado_domicilio_cliente'] = $array[0]->ban_verificado_domicilio_cliente;
        $rowTmp['ban_sender_mail_today'] = $array[0]->ban_sender_mail_today;
        $rowTmp['ban_fb_login_user'] = $array[0]->ban_fb_login_user;
        $rowTmp['ban_default_domicilio_cliente'] = $array[0]->ban_default_domicilio_cliente;
        $rowTmp['id_fb_user'] = $array[0]->id_fb_user;
        $rowTmp['vigencia_cliente'] = $array[0]->vigencia_cliente;
        $rowTmp['remember_token'] = $array[0]->remember_token;
        $rowTmp['updated_at'] = $array[0]->updated_at;
        $rowTmp['created_at'] = $array[0]->created_at;
        $rowTmp['latitud_domicilio_cliente'] = $array[0]->latitud_domicilio_cliente;
        $rowTmp['longitud_domicilio_cliente'] = $array[0]->longitud_domicilio_cliente;
        $rowTmp['app_lealtad_puntos_cliente'] = (!empty($array[0]->app_lealtad_puntos_cliente))?$array[0]->app_lealtad_puntos_cliente:0;
        $rowTmp['id_unidad'] = '';
        $rowTmp['cp_garantia_ube'] = '';
        $rowTmp['id_estado_republica'] = '';
        $rowTmp['id_ciudad_republica'] = '';
        $rowTmp['colonia_garantia_ube'] = '';
        $rowTmp['id_status_garantia_ube'] = '';
        $rowTmp['descripcion_estado_republica'] = '';
        $rowTmp['existe_ube_online'] = '';
        $rowTmp['valida_online_reg_form'] = '';
        $rowTmp['descripcion_ciudad_republica'] = '';

        array_push($arrayTmp, $rowTmp);
        return $arrayTmp;
    }

    public function getClientById(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getClientById') {
                        $dataClient['id_cliente'] = $data['client']['id'];

                        $rowClient = Client::select('id_colonias_garantia_ube')->where('id_cliente', $dataClient['id_cliente'])->get();
                        if ($rowClient[0]['id_colonias_garantia_ube'] > 0) {
                            $client = Client::getClientById($dataClient);
                            $othersAddress = Client::getAllRelAddressClientById($dataClient);
                            if (count($client) > 0) {
                                $response['status'] = 'OK';
                                $response['data']['client'] = $client;
                                if (count($othersAddress)) {
                                    $arrayExtraAddress = $this->parsingOthersAddress($othersAddress);
                                    $response['data']['extra_address'] = $arrayExtraAddress;
                                } else
                                    $response['data']['extra_address'] = [];
                                //Añadir domiciliosExtrasDeEntrega..
                                return response()->json($response);
                            } else {

                                $dataWrong['status'] = 'error';
                                $dataWrong['error']['code'] = 1003;
                                $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                                return response()->json($dataWrong);
                            }
                        } elseif ($rowClient[0]['id_colonias_garantia_ube'] == -1) {

                            $rowclient = Client::getClientNoAddressById($dataClient);
                            $client = $this->parsingClientNoAddress($rowclient);

                            $othersAddress = Client::getAllRelAddressClientById($dataClient);
                            if (count($client) > 0) {
                                $response['status'] = 'OK';
                                $response['data']['client'] = $client;
                                if (count($othersAddress)) {
                                    $arrayExtraAddress = $this->parsingOthersAddress($othersAddress);
                                    $response['data']['extra_address'] = $arrayExtraAddress;
                                } else
                                    $response['data']['extra_address'] = [];
                                //Añadir domiciliosExtrasDeEntrega..
                                return response()->json($response);
                            } else {

                                $dataWrong['status'] = 'error';
                                $dataWrong['error']['code'] = 1003;
                                $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                                return response()->json($dataWrong);
                            }
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

    public function getClientByEmail(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getClientByEmail') {
                        $dataClient['email_cliente'] = $data['client']['email'];
                        $rowClient = Client::select('id_colonias_garantia_ube')->where('email_cliente', $dataClient['email_cliente'])->get();
                        if ($rowClient[0]['id_colonias_garantia_ube'] > 0) {
                            $client = Client::getClientByEmail($dataClient);

                            if (count($client) > 0) {
                                $response['status'] = 'OK';
                                $response['data']['client'] = $client;
                                //Añadir domiciliosExtrasDeEntrega..
                                return response()->json($response);
                            } else {

                                $dataWrong['status'] = 'error';
                                $dataWrong['error']['code'] = 1003;
                                $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                                return response()->json($dataWrong);
                            }
                        } elseif ($rowClient[0]['id_colonias_garantia_ube'] == -1) {

                            $rowclient = Client::getClientNoAddressByEmail($dataClient);
                            $client = $this->parsingClientNoAddress($rowclient);

                            if (count($client) > 0) {
                                $response['status'] = 'OK';
                                $response['data']['client'] = $client;
                                return response()->json($response);
                            } else {

                                $dataWrong['status'] = 'error';
                                $dataWrong['error']['code'] = 1003;
                                $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                                return response()->json($dataWrong);
                            }
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

    public function register(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'registerClient') {
                        
                        $Client = new Client();

                        $Client->nombre_cliente = $data['client']['nombre_cliente'];
                        $Client->apellido_cliente = $data['client']['apellido_cliente'];
                        $Client->domicilio_cliente = $data['client']['domicilio_cliente'];
                        $Client->id_colonias_garantia_ube = $data['client']['id_colonias_garantia_ube'];
                        $Client->app_colonias_garantia_ube = (!empty($data['client']['app_colonias_garantia_ube']))?$data['client']['app_colonias_garantia_ube']:'';
                        $Client->numero_ext_cliente = $data['client']['numero_ext_cliente'];
                        $Client->numero_int_cliente = $data['client']['numero_int_cliente'];
                        $Client->lada_cliente = $data['client']['lada_cliente'];
                        $Client->telefono_cliente = $data['client']['telefono_cliente'];
                        $Client->celular_cliente = $data['client']['celular_cliente'];
                        $Client->cp_cliente = $data['client']['cp_cliente'];
                        $Client->email_cliente = $data['client']['email_cliente'];
                        $Client->passwd_cliente = $data['client']['passwd_cliente'];
                        $Client->id_tipo_orden = $data['client']['id_tipo_orden'];
                        $Client->fecha_cumple_cliente = $data['client']['fecha_cumple_cliente'];
                        $Client->mayoria_edad_cliente = $data['client']['mayoria_edad_cliente'];
                        $Client->referencia_domicilio_cliente = (!empty($data['client']['referencia_domicilio_cliente'])) ? $data['client']['referencia_domicilio_cliente'] : '';
                        $Client->fecha_alta_cliente = $data['client']['fecha_alta_cliente'];
                        $Client->fecha_nacimiento_cliente = (!empty($data['client']['fecha_nacimiento_cliente']))?$data['client']['fecha_nacimiento_cliente']:'';
                        $Client->fecha_last_login = $data['client']['fecha_last_login'];
                        $Client->sucursal_id_unidad = $data['client']['sucursal_id_unidad'];
                        $Client->status_activa_sugerencia = $data['client']['status_activa_sugerencia'];
                        $Client->ip_last_login = $data['client']['ip_last_login'];
                        $Client->http_user_agent = $data['client']['http_user_agent'];
                        $Client->ban_verificado_domicilio_cliente = $data['client']['ban_verificado_domicilio_cliente'];
                        $Client->ban_fb_login_user = $data['client']['ban_fb_login_user'];
                        $Client->ban_default_domicilio_cliente = $data['client']['ban_default_domicilio_cliente'];
                        $Client->id_fb_user = $data['client']['id_fb_user'];
                        $Client->vigencia_cliente = $data['client']['vigencia_cliente'];
                        $Client->remember_token = $data['client']['remember_token'];
                        $Client->updated_at = $data['client']['updated_at'];
                        $Client->updated_at = $data['client']['updated_at'];
                        $Client->latitud_domicilio_cliente = $data['client']['latitud_domicilio_cliente'];
                        $Client->longitud_domicilio_cliente = $data['client']['longitud_domicilio_cliente'];
                        $Client->user_id = (!empty($data['client']['user_id']))?$data['client']['user_id']:'0';
                        
                        $Client->save();

                        if ($Client->id > 0) {

                            $dataClient['client']['id_cliente'] = $Client->id;
                            $dataClient['client']['nombre_cliente'] = $Client->nombre_cliente;
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
    
    public function editClientById(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'editClientById') {
                        $data_update['id_cliente']= $data['client']['id'];
                        $data_update['data_update']=$data['data_client'];                       
                        $updateClient=Client::editClientById($data_update);
                        if ($updateClient > 0) {

                            $dataClientResponse['client']['id_cliente'] = $data['client']['id'];
                            // $dataClient['client']['nombre_cliente'] = $Client->nombre_cliente;
                            $response['status'] = 'OK';
                            $response['data'] = $dataClientResponse;
                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1006;
                            $dataWrong['error']['message'] = 'No se pudo actualizar en la base de datos';
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
    
    public function editAddressById(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'editAddressById') {
                        $dataClient['id_cliente'] = $data['client']['id'];

                        $dataValues = array(
                            //Si es domicilio alternativo(rel_domicilio_cliente) enviar false.
                            // 'id_rel_domicilio_cliente' => $data['address']['id_rel_domicilio_cliente'], //0 si primary_address=true, 1 si primary_address=false
                            'id_colonias_garantia_ube' => $data['address']['id_colonias_garantia_ube'], ///OBTENIDO DEL CATALOGO 8.- Catálogo de colonias
                            'app_colonias_garantia_ube' => $data['address']['app_colonias_garantia_ube'],
                            'sucursal_id_unidad' => $data['address']['sucursal_id_unidad'], ///OBTENIDO DEL CATALOGO 8.- Catálogo de colonias
                            'domicilio_cliente' => $data['address']['domicilio_cliente'], //varchar. Max 50
                            'numero_ext_cliente' => $data['address']['numero_ext_cliente'], //required varchar. Max 10
                            'numero_int_cliente' => $data['address']['numero_int_cliente'], //Varchar Max. 50
                            'referencia_domicilio_cliente' => $data['address']['referencia_domicilio_cliente'], //Tipo Text
                            'cp_cliente' => $data['address']['cp_cliente'], //Required int Max.8
                            'lada_cliente' => $data['address']['lada_cliente'], //required varchar 
                            'telefono_cliente' => $data['address']['telefono_cliente'], //required varchar sin clave lada
                            'latitud_domicilio_cliente' => $data['address']['latitud_domicilio_cliente'],
                            'longitud_domicilio_cliente' => $data['address']['longitud_domicilio_cliente'],
                            'celular_cliente' => $data['address']['celular_cliente'], //varchar,'
                        );

                        if ($data['address']['primary_address'] == true) {
                           // var_dump('aca');
                            $updateRow = Client::where('id_cliente', $dataClient['id_cliente'])->update($dataValues);
                        } else {
                            //
                            $dataUpdate['id_cliente'] = $data['client']['id'];
                            $dataUpdate['id_rel_domicilio_cliente'] = $data['address']['id_rel_domicilio_cliente'];
                            $dataUpdate['array_values'] = $dataValues;
                            $updateRow = Client::editRelAddressById($dataUpdate);
                        }



                        if ($updateRow > 0) {

                            $dataClientResponse['client']['id_cliente'] = $data['client']['id'];
                            // $dataClient['client']['nombre_cliente'] = $Client->nombre_cliente;
                            $response['status'] = 'OK';
                            $response['data'] = $dataClientResponse;
                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1006;
                            $dataWrong['error']['message'] = 'No se pudo actualizar en la base de datos';
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
    
    public function getAddressById(Request $request) {
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getAddressById') {
                        $dataAddress['id_cliente'] = $data['client']['id'];
                        $dataAddress['id_rel_domicilio_cliente'] = $data['address']['id'];
                        if (!empty($data['address']['id']) && $data['address']['id'] > 0) {

                            $rowAddress = Client::getNoAddressById($dataAddress);
                            $arrayAddress = $this->parsingOthersAddress($rowAddress);
                            //print_r($arrayAddress);
                            if (count($arrayAddress) > 0) {
                                // $dataClient['client']['nombre_cliente'] = $Client->nombre_cliente;
                                $response['status'] = 'OK';
                                $response['data'] = $arrayAddress;
                                return response()->json($response);
                            } else {
                                $dataWrong['status'] = 'error';
                                $dataWrong['error']['code'] = 1003;
                                $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                                return response()->json($dataWrong);
                            }
                        } else {

                            $rowClient = Client::getClientNoAddressById($dataAddress);
                            if (count($rowClient) > 0) { //IF CLIENT EXIST
                                if ($rowClient[0]->id_colonias_garantia_ube > 0) {
                                    $client = Client::getClientById($dataAddress);
                                    $arrayParsingAddress = $this->parsingMainAddress($client);
                                    $response['status'] = 'OK';
                                    $response['data'] = $arrayParsingAddress;
                                    return response()->json($response);
                                } elseif ($rowClient[0]->id_colonias_garantia_ube == -1) {

                                    $client = $this->parsingMainAddress($rowClient);
                                    $response['status'] = 'OK';
                                    $response['data'] = $client;
                                    return response()->json($response);
                                }
                            } else {
                                $dataWrong['status'] = 'error';
                                $dataWrong['error']['code'] = 1003;
                                $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
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
        } catch (\Exception $e) {
            $dataWrong['status'] = 'error';
            $dataWrong['error']['code'] = 1002;
            $dataWrong['error']['message'] = $e->getMessage();
            //header('Content-Type: application/json');
            return response()->json($dataWrong);
        }
    }

    public function deleteAddressById(Request $request) {
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'deleteAddressById') {
                        $dataAddress['id_cliente'] = $data['client']['id'];
                        $dataAddress['id_rel_domicilio_cliente'] = $data['address']['id'];
                      //  print_r($dataAddress);
                        
                        $deleteAddress = Client::deleteAddressClientById($dataAddress);                      
                        if ($deleteAddress > 0) {
                            // $dataClient['client']['nombre_cliente'] = $Client->nombre_cliente;
                            $response['status'] = 'OK';
                            //$response['data'] = $arrayAddress;
                            return response()->json($response);
                        } else {
                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1006;
                            $dataWrong['error']['message'] = 'No se pudo actualizar en la base de datos';
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

    public function addRelAddressClient(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'addAddressClient') {
                        $dataClient['id_cliente'] = $data['client']['id'];

                        $dataValues = array(
                            //Si es domicilio alternativo(rel_domicilio_cliente) enviar false.
                            // 'id_rel_domicilio_cliente' => $data['address']['id_rel_domicilio_cliente'], //0 si primary_address=true, 1 si primary_address=false
                            'id_colonias_garantia_ube' => $data['address']['id_colonias_garantia_ube'], ///OBTENIDO DEL CATALOGO 8.- Catálogo de colonias
                            'app_colonias_garantia_ube' => $data['address']['app_colonias_garantia_ube'],
                            'sucursal_id_unidad' => $data['address']['sucursal_id_unidad'], ///OBTENIDO DEL CATALOGO 8.- Catálogo de colonias
                            'domicilio_cliente' => $data['address']['domicilio_cliente'], //varchar. Max 50
                            'numero_ext_cliente' => $data['address']['numero_ext_cliente'], //required varchar. Max 10
                            'numero_int_cliente' => $data['address']['numero_int_cliente'], //Varchar Max. 50
                            'referencia_domicilio_cliente' => $data['address']['referencia_domicilio_cliente'], //Tipo Text
                            'cp_cliente' => $data['address']['cp_cliente'], //Required int Max.8
                            'lada_cliente' => $data['address']['lada_cliente'], //required varchar 
                            'telefono_cliente' => $data['address']['telefono_cliente'], //required varchar sin clave lada
                            'celular_cliente' => $data['address']['celular_cliente'], //varchar,'
                            ///newFields..
                            'id_cliente'=>$data['client']['id'],
                            'ban_verificado_domicilio_cliente'=>-1,
                            'ban_default_domicilio_cliente'=>0,
                            'vigencia_rel_domicilio_cliente'=>1,
                            'latitud_domicilio_cliente' =>$data['address']['latitud_domicilio_cliente'],
                            'longitud_domicilio_cliente' => $data['address']['longitud_domicilio_cliente'],
                            'fecha_agregado_domicilio_cliente'=>date("Y-m-d H:i:s")
                            
                            
                        );
                     
                        $insert = Client::addRelAddressClient($dataValues);

                        if ($insert > 0) {

                            $dataClientResponse['client']['id_cliente'] = $data['client']['id'];
                            $dataClientResponse['client']['id_rel_domicilio_cliente'] = $insert;
                            // $dataClient['client']['nombre_cliente'] = $Client->nombre_cliente;
                            $response['status'] = 'OK';
                            $response['data'] = $dataClientResponse;
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

}
