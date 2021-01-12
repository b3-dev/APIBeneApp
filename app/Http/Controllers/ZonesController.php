<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\Zone;

class ZonesController extends Controller
{
    //   
    public function zones(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            //   $Entradas = Product::where('id_categoria', $Constans['ENTRADAS'])->where('vigencia_articulo',1)      

            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getZones') {

                        $zones = Zone::All();
                        if (count($zones) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $zones;
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
