<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\TrackingOrder;

class TrackingOrderController extends Controller
{
    //
    public function getOnlineInfoStore(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getOnlineInfoStore') {

                        if (!empty($data['store']['id'])) {
                            //$params['id_unidad'] = $data['store']['id'];
                            $data['url'] = 'http://'.$Constans['IP_API_BACKEND'].'/ws/ubes.php?getInfoUbe=true&idUnidad='.$data['store']['id'];

                            $arrayContentPage = $this->manageCurlOnlineStatus($data);
                            return response($arrayContentPage);
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
    
    public function getOnlineStatusStore(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getOnlineStatus') {

                        if (!empty($data['store']['id'])) {
                            //$params['id_unidad'] = $data['store']['id'];
                            $data['url'] = 'http://'.$Constans['IP_API_BACKEND'].'/ws/ubes.php?getOnlineStatus=true&idUnidad=' . $data['store']['id'];

                            $arrayContentPage = $this->manageCurlOnlineStatus($data);
                            return response($arrayContentPage);
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
    
    public function setDeliveredOrder(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'setDeliveredOrder') {

                        if (!empty($data['store']['id']) && !empty($data['order']['id'])) {
                            
                            $dataRequest['url'] = 'http://' . $Constans['IP_API_BACKEND'] . '/ws/employe.php?setOrderToDelivered=true';
                            $arrayParams = array(
                                'store' => array(
                                    'id' => $data['store']['id'], //API KEY
                                ),
                                'order' => array(//
                                    'id' => $data['order']['id'], //inT.  
                                ),
                            );

                            $dataJsonPost = json_encode($arrayParams);
                            $dataRequest['post'] = $dataJsonPost;

                            $arrayContentPage = $this->manageCurlPostService($dataRequest);
                            return response($arrayContentPage);
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

    public function getAssignedOrders(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getAssignedOrders') {

                        if (!empty($data['store']['id'])) {
                            //$params['id_unidad'] = $data['store']['id'];
                            $dataRequest['url'] = 'http://'.$Constans['IP_API_BACKEND'].'/ws/employe.php?getAssignedOrders=true';
                            $arrayParams = array(
                                'store' => array(
                                    'id' => $data['store']['id'], //API KEY
                                ),
                                'employe' => array(//
                                    'id' => $data['employe']['id'], //inT.  
                                ),
                            );

                            $dataJsonPost = json_encode($arrayParams);
                            $dataRequest['post']=$dataJsonPost;
                            
                            $arrayContentPage = $this->manageCurlPostService($dataRequest);
                            
                            return response($arrayContentPage);
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
    
     public function getInProccessOrders(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getInProccessOrders') {

                        if (!empty($data['store']['id'])) {
                            //$params['id_unidad'] = $data['store']['id'];
                            $dataRequest['url'] = 'http://'.$Constans['IP_API_BACKEND'].'/ws/employe.php?getInProccessOrders=true';
                            $arrayParams = array(
                                'store' => array(
                                    'id' => $data['store']['id'], //API KEY
                                ),
                            );

                            $dataJsonPost = json_encode($arrayParams);
                            $dataRequest['post']=$dataJsonPost;
                            
                            $arrayContentPage = $this->manageCurlPostService($dataRequest);
                            
                            return response($arrayContentPage);
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
    
    
    public function getInfoEmploye(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getInfoEmploye') {
                        if (!empty($data['store']['id'])) {
                            //$params['id_unidad'] = $data['store']['id'];
                            $dataRequest['url'] = 'http://'.$Constans['IP_API_BACKEND'].'/ws/employe.php?getInfoEmploye=true';
                            $arrayParams = array(
                                'store' => array(
                                    'id' => $data['store']['id'], //API KEY
                                ),
                                'employe' => array(//
                                    'email' => $data['employe']['email'], //inT.  
                                ),
                            );
                                                       
                            $dataJsonPost = json_encode($arrayParams);
                            $dataRequest['post']=$dataJsonPost;
                            $arrayContentPage = $this->manageCurlPostService($dataRequest);
                            return response($arrayContentPage);
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
    
    public function getAdminByEmail(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getAdminByEmail') {
                        if (!empty($data['admin']['email'])) {
                            $params['email_admin_user'] = $data['admin']['email'];
                            $arrayAdmin = TrackingOrder::getAdminByEmail($params);
                            if (count($arrayAdmin) > 0) {
                                $response['status'] = 'OK';
                                $response['data'] = $arrayAdmin;
                                return response()->json($response);
                            } else {
                                $dataWrong['status'] = 'error';
                                $dataWrong['error']['code'] = 1003;
                                $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                                return response()->json($zones);
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
    
    public function getAdminStores(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getAdminStores') {
                        if (!empty($data['admin']['id'])) {
                            $params['id_app_admin_user']=$data['admin']['id'];
                            $arrayStores= TrackingOrder::getAdminStoresById($params);
                            if (count($arrayStores) > 0) {
                                $response['status'] = 'OK';
                                $response['data'] = $arrayStores;
                                return response()->json($response);
                            } else {
                                $dataWrong['status'] = 'error';
                                $dataWrong['error']['code'] = 1003;
                                $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                                return response()->json($zones);
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
    
    public function getAdminStoreById(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getAminStoresById') {
                        if (!empty($data['store']['id'])) {
                            $params['id_unidad'] = $data['store']['id'];
                            $arrayStores = TrackingOrder::getAdminStoreById($params);
                            if (count($arrayStores) > 0) {
                                $response['status'] = 'OK';
                                $response['data'] = $arrayStores;
                                return response()->json($response);
                            } else {
                                $dataWrong['status'] = 'error';
                                $dataWrong['error']['code'] = 1003;
                                $dataWrong['error']['message'] = 'Item(s) no localizado(s)';
                                return response()->json($zones);
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
    
    public function getActiveEmployeOnStore(Request $request) {
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                
                if ($data['token']['id'] == $Constans['API_KEY']) {
                    if ($data['action'] == 'getActiveEmployeOnStore') {
                        if (!empty($data['store']['id'])) {
                           // dd($data);
                           $dataRequest['url'] = 'http://'.$Constans['IP_API_BACKEND'].'/ws/employe.php?getActiveEmployeOnStore=true';
                           $arrayParams = array(
                                'store' => array(
                                    'id' => $data['store']['id'], //API KEY
                                ),
                            );                                                      
                            $dataJsonPost = json_encode($arrayParams);
                            $dataRequest['post']=$dataJsonPost;
                            $arrayContentPage = $this->manageCurlPostService($dataRequest);
                            return response($arrayContentPage);
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
            return response()->json($dataWrong);
        }
    }
    //http://201.155.236.67/ws/ubes.php?getOnlineStatus=true&&idUnidad=10
      
     public function manageCurlOnlineStatus($data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_URL, $data['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);  //indefinidamente
        curl_setopt($ch, CURLOPT_TIMEOUT, 100000);
        $content = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        return $content;
    }
    
    public function manageCurlPostService($data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $data['url']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);  //indefinidamente
        curl_setopt($ch, CURLOPT_TIMEOUT, 100000);
        $content = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        return $content;
    }
    

}
