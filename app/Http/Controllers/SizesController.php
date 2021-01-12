<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
Use Illuminate\Support\Facades\Config;
use App\Size;


class SizesController extends Controller
{
    //
        
    
    public function parsingArraySizes($arraySizes){
        $rowTmp = array();
        $arrayTmp = array();
        foreach($arraySizes as $sizes){
            $rowTmp=$sizes;
            $data['id_tamano_articulo']=$sizes->id_tamano_articulo;
            $rowTmp['data_images_by_sizes'] =Size::getRelImgBySizeId($data);
            array_push($arrayTmp, $rowTmp);
        }
        
        return $arrayTmp;
    }
    
     public function sizes(Request $request) {
       // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getSizes') {

                        $sizes = Size::where('app_vigencia_tamano_articulo', 1)->get();
                        if (count($sizes) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $sizes;
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
    
    public function getSizesByCategoryId(Request $request) {
       // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getSizesByCategoryId') {

                        $sizes = Size::where('app_vigencia_tamano_articulo', 1)
                                ->where('id_categoria',$data['category']['id'])
                                ->get();
                        
                       $arraySizes= $this->parsingArraySizes($sizes);
                        
                        if (count($arraySizes) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $arraySizes;
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
    
    public function getSizeById(Request $request) {
       // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getSizeById') {

                        $sizes = Size::where('id_tamano_articulo',$data['size']['id'])
                                ->get();
                        if (count($sizes) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $sizes;
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
