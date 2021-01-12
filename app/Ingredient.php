<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
Use Illuminate\Support\Facades\DB;

class Ingredient extends Model
{
    //
    protected $table = 'ingrediente_pizza';
    
    public static function getCategoryIngredients() {
        $categoryIngredients = DB::table('categoria_ingrediente')
                //   ->join('cliente', 'rel_domicilio_cliente.id_cliente', '=', 'cliente.id_cliente')
                ->where('vigencia_categoria_ingrediente', 1)
                ->orderBy('id_categoria_ingrediente', 'asc')
                ->get();
        //dd($recordClient);
        return $categoryIngredients;
    }
    
    public static function getCategoryIngredientById($id_categoria_ingrediente) {
        $categoryIngredients = DB::table('categoria_ingrediente')
                ->where('id_categoria_ingrediente', $id_categoria_ingrediente)
                ->get();
        return $categoryIngredients;
    }
    
    public static function getExtrachessePrice($data) {
        $extrachesse = DB::table('rel_ingrediente_tamano')
                ->where('id_ingrediente_pizza', $data['id_ingrediente_pizza'])
                ->where('id_tamano_articulo', $data['id_tamano_articulo'])
                ->get();
        return $extrachesse;
    }
    
    public static function getChesseBorderPrice($data) {
        $extrachesse = DB::table('rel_ingrediente_tamano')
                ->where('id_ingrediente_pizza', $data['id_ingrediente_pizza'])
                ->where('id_tamano_articulo', $data['id_tamano_articulo'])
                ->get();
        return $extrachesse;
    }
    
    //chesse border..
    
    public static function getChesseBorderItems(){
        $extrachesse = DB::table('tipo_orilla_queso')
                ->where('app_vigencia_orilla_queso', 1)
                ->orderBy('id_tipo_orilla_queso', 'ASC')
                ->get();
        return $extrachesse;
    }
    
    public static function getChesseBorderItemByParam($data){
         $chesseBorder = DB::table('tipo_orilla_queso')
                    ->join('ingrediente_pizza', 'ingrediente_pizza.id_ingrediente_pizza', '=', 'tipo_orilla_queso.id_ingrediente_pizza')
                ->where('id_tipo_orilla_queso', $data['id_tipo_orilla_queso'])
                ->get();
        return $chesseBorder;
    }
    
    public static function getPanpizzaPrice($data) {
        $panpizza = DB::table('rel_ingrediente_tamano')
                ->where('id_ingrediente_pizza', $data['id_ingrediente_pizza'])
                ->where('id_tamano_articulo', $data['id_tamano_articulo'])
                ->get();
        return $panpizza;
    }
    
    public static function getIngredientsBySpecialty($data) {
        $extrachesse = DB::table('rel_ingrediente_articulo')
                ->join('ingrediente_pizza', 'ingrediente_pizza.id_ingrediente_pizza', '=', 'rel_ingrediente_articulo.id_ingrediente_pizza')
                ->where('rel_ingrediente_articulo.id_articulo', $data['id_articulo'])
                ->where('ingrediente_pizza.ban_display_ingrediente', 1)
                ->where('rel_ingrediente_articulo.id_ingrediente_pizza', '<>', 21) //21=extra queso
                ->get();
        return $extrachesse;
    }

    public static function getRelImgSizeIngredientByd($data) {
        $relImgSizeIngredient = DB::table('app_rel_img_ingrediente_tamano')
                ->join('tamano_articulo', 'tamano_articulo.id_tamano_articulo', '=', 'app_rel_img_ingrediente_tamano.id_tamano_articulo')
                ->where('app_rel_img_ingrediente_tamano.id_ingrediente_pizza', $data['id_ingrediente_pizza'])
                ->orderBy('app_rel_img_ingrediente_tamano.id_tamano_articulo', 'asc')
                ->get();
        return $relImgSizeIngredient;
    }
    
    public static function getRelPricesByIngredient($data){
        $relPriceBysize = DB::table('rel_ingrediente_tamano')
                ->where('id_ingrediente_pizza',$data['id_ingrediente_pizza'])
                ->orderBy('id_tamano_articulo', 'asc')
                ->get();
         
        return $relPriceBysize;
    }
      
    public static function getIngredientsByArray($data) {
        $rowProduct = DB::table('ingrediente_pizza')
                ->whereIn('id_ingrediente_pizza', $data['arrayIngredients'])
                ->orderBy('prioridad_lista_ingrediente', 'ASC')
                ->get(); //vigente..

        return $rowProduct;
    }

}
