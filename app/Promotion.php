<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
Use Illuminate\Support\Facades\DB;


class Promotion extends Model
{
    //
   // protected $table = 'articulo';
     
      
    public static function getPromotions() {
        $rowProduct = DB::table('articulo')
                         ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                        ->where('articulo.id_categoria', 7)
                        ->where('articulo.app_vigencia_articulo', 1)
                        ->orderBy('articulo.prioridad_orden_articulo','asc')
                        ->get(); //vigente..

        return $rowProduct;
    }
    
     public static function getPromotionsPerStore($data) {
        $rowProduct = DB::table('articulo')
                         ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                        ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                        ->where('articulo.id_categoria', 7)
                        ->where('articulo.app_vigencia_articulo', 1)
                        ->where('rel_articulo_tamano.id_unidad', $data['id_unidad'])
                        ->where('rel_articulo_tamano.vigencia_producto_unidad', 1)
                        ->orderBy('articulo.prioridad_orden_articulo','asc')
                        ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getPromotionsById($data) {
        $rowProduct = DB::table('articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', 7)
               // ->where('articulo.app_vigencia_articulo', 1)
                ->where('articulo.id_articulo', $data['id_articulo'])
                ->orderBy('articulo.prioridad_orden_articulo','asc')
                ->get(); //vigente..

        return $rowProduct;
    }

    public static function getIncludesProductsByPromotion($id_paquete_web_articulo) {
        $rowProduct = DB::table('app_rel_paquete_articulo')
                ->join('articulo', 'articulo.id_articulo', '=', 'app_rel_paquete_articulo.id_articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                //   ->join('tamano_articulo', 'tamano_articulo.id_tamano_articulo', '=', 'app_rel_paquete_articulo.id_tamano_articulo')
                ->where('app_rel_paquete_articulo.id_paquete_web_articulo', $id_paquete_web_articulo)
                ->where('app_rel_paquete_articulo.app_vigencia_rel_paquete_articulo', 1)
                ->orderBy('app_rel_paquete_articulo.prioridad_orden_paquete_articulo', 'asc')
                ->get(); //vigente..

        return $rowProduct;
    }

    public static function getIncludesSizesProductsByPromotion($id_tamano_articulo) {
        $rowProduct = DB::table('tamano_articulo')
                ->where('tamano_articulo', 1)
                ->where('id_tamano_articulo', $id_tamano_articulo)
                ->get(); //vigente..

        return $rowProduct;
    }

     public static function getConfigBasePizzaByPromotion($id_app_config_base_pizza_articulo) {
        $rowProduct = DB::table('app_config_base_pizza_articulo')
                ->where('id_app_config_base_pizza_articulo', $id_app_config_base_pizza_articulo)
                ->get(); 
        return $rowProduct;
    }

    
    public static function getPricePromotions($id_articulo) {
        $rowProduct = DB::table('rel_articulo_tamano')
                        ->where('id_articulo',$id_articulo )
                        ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getPricePromotionsPerStore($data) {
        $rowProduct = DB::table('rel_articulo_tamano')
                        ->where('id_articulo',$data['id_articulo'] )
                        ->where('id_unidad',$data['id_unidad'] )                     
                        ->get(); //vigente..

        return $rowProduct;
    }
    
   

}
