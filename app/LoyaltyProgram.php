<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
Use Illuminate\Support\Facades\DB;

class LoyaltyProgram extends Model
{
    //
    public static function addLoyaltyPoints($array) {

        $insert = DB::table('app_lealtad_nuevos_puntos_cliente')->insertGetId($array);
        return $insert;
    }
    
    public static function changeLoyaltyPoints($array) {

        $insert = DB::table('app_lealtad_canje_puntos_cliente')->insertGetId($array);
        return $insert;
    }
    
    public static function getLoyaltyPromotions() {
        $rowProduct = DB::table('articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', 15)
                ->where('articulo.app_vigencia_articulo', 1)
                ->orderBy('articulo.prioridad_orden_articulo', 'asc')
                ->get(); //vigente..

        return $rowProduct;
    }

    public static function getLoyaltyPromotionsById($data) {
        $rowProduct = DB::table('articulo')
                ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->where('articulo.id_categoria', 15)
                ->where('articulo.app_vigencia_articulo', 1)
                ->where('articulo.id_articulo', $data['id_articulo'])
                ->orderBy('articulo.prioridad_orden_articulo', 'asc')
                ->get(); //vigente..

        return $rowProduct;
    }

}
