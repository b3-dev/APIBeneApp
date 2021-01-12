<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
Use Illuminate\Support\Facades\DB;


class Product extends Model
{
    //
    protected $table = 'articulo';
     
    public function category() {
        return $this->belongsTo('App\Category', 'id_categoria','id_categoria');
    }
    
    
    public static function getProducts() {
        $rowProducts = DB::table('articulo')
                        ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                        ->where('articulo.app_vigencia_articulo', 1)->get(); //vigente..
        return $rowProducts;
    }
    
     public static function getProductsPerStore($data) {
        $rowProducts = DB::table('articulo')
                        ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                         ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                        ->where('articulo.app_vigencia_articulo', 1) //vigente..
                        ->where('rel_articulo_tamano.id_unidad',$data['id_unidad'])
                        ->where('rel_articulo_tamano.vigencia_producto_unidad',1)
                        ->groupBy('articulo.id_articulo')->get();
        return $rowProducts;
    }

    public static function getProductById($data) {
        $rowProduct = DB::table('articulo')
                        ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                        ->where('articulo.id_articulo', $data['id_articulo'])
                       // ->where('articulo.app_vigencia_articulo', 1)
                        ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getProductByParamId($data) {
        $rowProduct = DB::table('articulo')
                        ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                        ->where('articulo.id_articulo', $data['id_articulo'])
                        //->where('articulo.app_vigencia_articulo', 1)
                        ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getProductsByString($data) {
        $rowProduct = DB::table('articulo')
                        ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                        ->where('articulo.app_nombre_articulo', 'like', '%' . $data['name'] . '%')
                        ->where('articulo.app_vigencia_articulo', 1)->get(); //vigente..

        return $rowProduct;
    }

    public static function getNutritionalInfoById($data) {
        $rowInfo = DB::table('app_calculadora_nutricion_articulo')
                ->where('app_calculadora_nutricion_articulo.id_articulo', $data['id_articulo'])
                ->get(); //vigente..

        return $rowInfo;
    }

    public static function getProductPrice($data) {
        $rowProduct = DB::table('rel_articulo_tamano')
                ->where('id_articulo', $data['id_articulo'])
                ->where('id_tamano_articulo', $data['id_tamano_articulo'])
                ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getProductPricePerStore($data) {
        $rowProduct = DB::table('rel_articulo_tamano')
                ->where('id_articulo', $data['id_articulo'])
                 ->where('id_unidad', $data['id_unidad'])
                ->where('id_tamano_articulo', $data['id_tamano_articulo'])
                ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getPriceExtrasByParams($data) {
        $rowProduct = DB::table('rel_ingrediente_tamano')
                ->where('id_ingrediente_pizza', $data['id_ingrediente_pizza'])
                ->where('id_tamano_articulo', $data['id_tamano_articulo'])
                ->get(); //vigente..

        return $rowProduct;
    }


    public static function getProductSizes($data) {
        $rowProduct = DB::table('rel_articulo_tamano')
                ->join('tamano_articulo', 'rel_articulo_tamano.id_tamano_articulo', '=', 'tamano_articulo.id_tamano_articulo')
                ->where('rel_articulo_tamano.id_articulo', $data['id_articulo'])
                ->where('rel_articulo_tamano.id_unidad', $data['id_unidad'])
                ->where('rel_articulo_tamano.vigencia_producto_unidad',1)
                ->where('tamano_articulo.app_vigencia_tamano_articulo', 1)
                ->orderBy('tamano_articulo.prioridad_orden_articulo','ASC')
                ->get(); //vigente..
        return $rowProduct;
    }
    
     public static function getProductSizesPerStore($data) {
        $rowProduct = DB::table('rel_articulo_tamano')
                ->join('tamano_articulo', 'rel_articulo_tamano.id_tamano_articulo', '=', 'tamano_articulo.id_tamano_articulo')
                ->where('rel_articulo_tamano.id_articulo', $data['id_articulo'])
                ->where('rel_articulo_tamano.id_unidad',$data['id_unidad'])
                 ->where('rel_articulo_tamano.vigencia_producto_unidad', 1)
                 ->where('tamano_articulo.app_vigencia_tamano_articulo', 1)
                ->orderBy('tamano_articulo.prioridad_orden_articulo','ASC')
                ->get(); //vigente..
        return $rowProduct;
    }

    public static function getProductsByCategory($data) {
        $rowProduct = DB::table('articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', $data['id_categoria'])
                ->where('articulo.app_vigencia_articulo', 1)
                ->orderBy('articulo.prioridad_orden_articulo','ASC')
                ->get(); //vigente..

        return $rowProduct;
    }
    
     public static function getProductsByCategoryPerStore($data) {
        $rowProduct = DB::table('articulo')
                ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', $data['id_categoria'])
                ->where('rel_articulo_tamano.id_unidad', $data['id_unidad'])
                ->where('rel_articulo_tamano.vigencia_producto_unidad',1)
                ->where('articulo.app_vigencia_articulo', 1)
                ->groupBy('articulo.id_articulo')
                ->orderBy('articulo.prioridad_orden_articulo','ASC')
                ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getProductsByArray($data) {
        $rowProduct = DB::table('articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', $data['id_categoria'])
                ->where('articulo.app_vigencia_articulo', 1)
                ->whereIn('articulo.id_articulo',$data['arraySpecialities'])
                ->orderBy('articulo.prioridad_orden_articulo','ASC')
                ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getRandomProduct($data) {

        $rowProduct = DB::table('rel_articulo_tamano')
                ->join('articulo', 'rel_articulo_tamano.id_articulo', '=', 'articulo.id_articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->join('tamano_articulo', 'rel_articulo_tamano.id_tamano_articulo', '=', 'tamano_articulo.id_tamano_articulo')
                ->where('articulo.app_vigencia_articulo', 1)
                ->where('tamano_articulo.app_vigencia_tamano_articulo', 1)
                ->where('articulo.id_articulo', $data['id_articulo'])
                ->orderBy(DB::raw('RAND()'))
                ->limit(2)
                ->get(); //vigente..

        return $rowProduct;
        
    }

    public static function getRandomProductPerStore($data) {

        $rowProduct = DB::table('rel_articulo_tamano')
                ->join('articulo', 'rel_articulo_tamano.id_articulo', '=', 'articulo.id_articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->join('tamano_articulo', 'rel_articulo_tamano.id_tamano_articulo', '=', 'tamano_articulo.id_tamano_articulo')
                ->where('articulo.app_vigencia_articulo', 1)
                ->where('tamano_articulo.app_vigencia_tamano_articulo', 1)
                ->where('articulo.id_articulo', $data['id_articulo'])
                ->where('rel_articulo_tamano.id_unidad', $data['id_unidad'])
                ->where('rel_articulo_tamano.vigencia_producto_unidad',1)
                ->orderBy(DB::raw('RAND()'))
                ->limit(2)
                ->get(); //vigente..

        return $rowProduct;
        
    }
    
    public static function getRandomProductbyCategory($data) {

        $rowProduct = DB::table('rel_articulo_tamano')
                ->join('articulo', 'rel_articulo_tamano.id_articulo', '=', 'articulo.id_articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->join('tamano_articulo', 'rel_articulo_tamano.id_tamano_articulo', '=', 'tamano_articulo.id_tamano_articulo')
                ->where('articulo.app_vigencia_articulo', 1)
                 ->where('articulo.id_categoria', $data['id_categoria'])
                ->where('tamano_articulo.app_vigencia_tamano_articulo', 1)
               // ->where('articulo.id_articulo', $data['id_articulo'])
                ->orderBy(DB::raw('RAND()'))
                ->limit(2)
                ->get(); //vigente..

        return $rowProduct;
        
    }
    
      public static function getRandomProductbyCategoryPerStore($data) {

        $rowProduct = DB::table('rel_articulo_tamano')
                ->join('articulo', 'rel_articulo_tamano.id_articulo', '=', 'articulo.id_articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->join('tamano_articulo', 'rel_articulo_tamano.id_tamano_articulo', '=', 'tamano_articulo.id_tamano_articulo')
                ->where('articulo.app_vigencia_articulo', 1)
                 ->where('articulo.id_categoria', $data['id_categoria'])
                ->where('tamano_articulo.app_vigencia_tamano_articulo', 1)
                ->where('rel_articulo_tamano.id_unidad', $data['id_unidad'])
                ->where('rel_articulo_tamano.vigencia_producto_unidad',1)
                ->orderBy(DB::raw('RAND()'))
                ->limit(1)
                ->get(); //vigente..

        return $rowProduct;
        
    }
    

    public static function getProductsByCategoryAndSubcategory($data) {
        $rowProduct = DB::table('articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', $data['id_categoria'])
                ->where('articulo.id_subcategoria', $data['id_subcategoria'])
                ->orderBy('articulo.prioridad_orden_articulo','ASC')
                ->where('articulo.app_vigencia_articulo', 1)
                ->get(); //vigente..

        return $rowProduct;
    }
    
     public static function getProductsByCategoryAndSubcategoryPerStore($data) {
        $rowProduct = DB::table('articulo')
                ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', $data['id_categoria'])
                ->where('articulo.id_subcategoria', $data['id_subcategoria'])
                ->where('rel_articulo_tamano.id_unidad', $data['id_unidad'])
                ->where('rel_articulo_tamano.vigencia_producto_unidad',1)
                ->where('articulo.app_vigencia_articulo', 1)
                ->groupBy('articulo.id_articulo')
                ->orderBy('articulo.prioridad_orden_articulo','ASC')
                ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getPriceByCategorieSubcategoryAndSize($data) {
        $rowProduct = DB::table('articulo')
                ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', $data['id_categoria'])
                ->where('articulo.id_subcategoria', $data['id_subcategoria'])
                ->where('rel_articulo_tamano.id_tamano_articulo', $data['id_tamano_articulo'])
                ->where('articulo.app_vigencia_articulo', 1)
               // ->orderBy('articulo.prioridad_orden_articulo', 'ASC')
                ->limit(1)
                ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getPriceByCategorieSubcategoryAndSizePerStore($data) {
        $rowProduct = DB::table('articulo')
                ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', $data['id_categoria'])
                ->where('articulo.id_subcategoria', $data['id_subcategoria'])
                ->where('rel_articulo_tamano.id_unidad', $data['id_unidad'])
                ->where('rel_articulo_tamano.vigencia_producto_unidad', 1)
                ->where('rel_articulo_tamano.id_tamano_articulo', $data['id_tamano_articulo'])
                ->where('articulo.app_vigencia_articulo', 1)
               // ->orderBy('articulo.prioridad_orden_articulo', 'ASC')
                ->limit(1)
                ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getPriceByCategorieAndSize($data) {
        $rowProduct = DB::table('articulo')
                ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', $data['id_categoria'])
                ->where('rel_articulo_tamano.id_tamano_articulo', $data['id_tamano_articulo'])
                ->where('articulo.app_vigencia_articulo', 1)
               // ->orderBy('articulo.prioridad_orden_articulo', 'ASC')
                ->limit(1)
                ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getPriceByCategorieAndSizePerStore($data) {
        $rowProduct = DB::table('articulo')
                ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', $data['id_categoria'])
                ->where('rel_articulo_tamano.id_tamano_articulo', $data['id_tamano_articulo'])
                ->where('rel_articulo_tamano.id_unidad', $data['id_unidad'])
                ->where('rel_articulo_tamano.vigencia_producto_unidad', 1)
                ->where('articulo.app_vigencia_articulo', 1)
               // ->orderBy('articulo.prioridad_orden_articulo', 'ASC')
                ->limit(1)
                ->get(); //vigente..

        return $rowProduct;
    }
  
    public static function getRelImgSizeProductId($data) {

        $rowImgProducts = DB::table('app_rel_img_base_especialidad_tamano')
                ->where('id_articulo', $data['id_articulo'])
                ->get(); //vigente..

        return $rowImgProducts;
    }
    
    public static function getAppVmxValueOnTable($data) {
        $rowData = DB::table('app_rel_valor_ventamaxx')
                ->where('id_articulo', $data['id_articulo'])
                ->where('id_tamano_articulo', $data['id_tamano_articulo'])
                ->get(); //vigente..

        return $rowData;
    }

}
