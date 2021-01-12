<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\Category;
use App\Subcategory;
use App\Product;

class CategoriesController extends Controller
{
    //
    public function categories(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getCategories') {

                        $categories = Category::where('app_vigencia_categoria', 1)
                                ->orderBy('app_prioridad_categoria','asc')
                                ->get();
                        if (count($categories) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $categories;
                            return response()->json($response);
                        } else {

                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1003;
                            $dataWrong['error']['message'] = 'Item no localizado';
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

    public function getCategoryById(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getCategoryById') {

                        $category = Category::where('id_categoria',$data['category']['id'] )->get();
                        if (count($category) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $category;
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
    /*subcategirues*/
     
    public function subcategories(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getSubcategories') {

                        $subcategories = Subcategory::where('vigencia_subcategoria', 1)->get();
                        $arrayParsingSubcategories =$this->parsingArraySubcategories($subcategories);
                        
                        if (count($arrayParsingSubcategories) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $arrayParsingSubcategories;
                            return response()->json($response);
                        } else {

                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1003;
                            $dataWrong['error']['message'] = 'Item no localizado';
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

    public function getSubcategoryById(Request $request) {
        // echo 'aca';
        try {
            $data = $request->json()->all();
            $Constans = Config::get('constants.options');
            if (count($data) > 0) {
                if ($data['token']['id'] == $Constans['API_KEY']) {

                    if ($data['action'] == 'getSubcategoryById') {

                        $subcategories = Subcategory::where('id_subcategoria', $data['subcategory']['id'])->get();
                        if (count($subcategories) > 0) {
                            $response['status'] = 'OK';
                            $response['data'] = $subcategories;
                            return response()->json($response);
                        } else {

                            $dataWrong['status'] = 'error';
                            $dataWrong['error']['code'] = 1003;
                            $dataWrong['error']['message'] = 'Item no localizado';
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
    
    function parsingArraySubcategories($subcategories) {

        $rowTmp = array();
        $arrayTmp = array();

        foreach ($subcategories as $subcategorie) {

            /* 'id_subcategoria' => int 1
              'nombre_subcategoria' => string 'Refresco Pepsi' (length=14)
              'vigencia_subcategoria' => int 1
             */
            $rowTmp['id_subcategoria'] = $subcategorie->id_subcategoria;
            $rowTmp['nombre_subcategoria'] = $subcategorie->nombre_subcategoria;
            $rowTmp['vigencia_subcategoria'] = $subcategorie->vigencia_subcategoria;

            $dataBaseProduct['id_articulo'] = $subcategorie->id_app_base_articulo;
            $dataBaseProduct['id_tamano_articulo'] = $subcategorie->id_app_tamano_articulo;

            $arraySizes = Product::getProductSizes($dataBaseProduct);

            $rowTmp['sizes'] = $arraySizes;

            $arrayImage = Product::getProductById($dataBaseProduct);
            $rowTmp['product_image'] = $arrayImage;


            array_push($arrayTmp, $rowTmp);
        }

        return $arrayTmp;
    }

}
