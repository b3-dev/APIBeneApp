<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
Use Illuminate\Support\Facades\DB;


class Processor extends Model
{
    //

    public static function getNotProcessedOrders($data) {
        $arrayOrders = DB::table('procesado_orden_pedido')
                ->join('procesado_articulo_pedido', 'procesado_orden_pedido.id_procesado_orden_pedido', '=', 'procesado_articulo_pedido.id_procesado_orden_pedido')
                ->join('tipo_pago', 'procesado_orden_pedido.id_tipo_pago', '=', 'tipo_pago.id_tipo_pago')
                ->where('procesado_orden_pedido.ban_procesado_orden_pedido', 0)
                ->where('procesado_orden_pedido.id_unidad', $data['id_unidad'])
                ->where('procesado_orden_pedido.ban_app_orden', 1)
                ->select('procesado_orden_pedido.id_procesado_orden_pedido',
                        'procesado_orden_pedido.id_cliente',
                        'procesado_orden_pedido.total_compra_orden_pedido', 
                        'procesado_orden_pedido.pago_cliente_orden_pedido', 
                        'procesado_orden_pedido.comentario_orden_pedido',
                        'procesado_orden_pedido.id_tipo_orden',
                        'procesado_orden_pedido.id_tipo_pago',
                        'procesado_orden_pedido.id_unidad',
                        'procesado_orden_pedido.date_recoger_orden_pedido', 
                        'procesado_orden_pedido.id_rel_domicilio_cliente',
                        'tipo_pago.descripcion_vmx_tipo_pago')
                ->distinct()
                //   ->orderBy('procesado_orden_pedido.date_agregado_orden_pedido', 'ASC')
                ->get(); //vigente..

        return $arrayOrders;
    }

    public static function getProductsbyOrder($data) {

        $arrayProducts = DB::table('procesado_articulo_pedido')
                ->join('procesado_orden_pedido', 'procesado_articulo_pedido.id_procesado_orden_pedido', '=', 'procesado_orden_pedido.id_procesado_orden_pedido')
                ->join('articulo', 'procesado_articulo_pedido.id_articulo', '=', 'articulo.id_articulo')
                //  ->join('categoria', 'articulo.id_categoria', '=', 'categoria.id_categoria')
                ->join('tamano_articulo', 'procesado_articulo_pedido.id_tamano_articulo', '=', 'tamano_articulo.id_tamano_articulo')
                ->where('procesado_articulo_pedido.id_procesado_orden_pedido', $data['id_procesado_orden_pedido'])
                //  ->where('procesado_orden_pedido.ban_app_orden', 1)
                ->orderBy('procesado_articulo_pedido.id_procesado_articulo_pedido', 'ASC')
                ->get(); //vigente..

        return $arrayProducts;
    }

    public static function getInfoDiscountCodeById($data) {

        $arrayDiscount = DB::table('procesado_orden_codigo_promocion')
                ->where('id_procesado_articulo_pedido', $data['id_procesado_articulo_pedido'])
                ->where('id_articulo', $data['id_articulo'])
                ->where('id_tamano_articulo', $data['id_tamano_articulo'])
                ->get();

        return $arrayDiscount;
    }

    public static function getProcesadoOrderPizzaBuilder($data) {
        $arrayPizzaBuilder = DB::table('procesado_orden_pizza_builder')
                ->where('id_procesado_articulo_pedido', $data['id_procesado_articulo_pedido'])
                ->orderBy('id_procesado_orden_pizza_builder', 'DESC')
                ->limit(1)
                ->get();

        return $arrayPizzaBuilder;
    }

    public static function getProcesadoOrderConfigPizzabyId($data) {
        $arrayConfigPizza = DB::table('procesado_orden_config_pizza')
                ->where('id_procesado_orden_pizza_builder', $data['id_procesado_orden_pizza_builder'])
                ->orderBy('id_procesado_orden_pizza_builder', 'ASC')
                ->get();
        return $arrayConfigPizza;
    }
    
    
    public static function getPizzaBaseOrSpecialityById($data) {
        //LOOKS FOR BASE PIZZA OR SPECIALITY BASE PIZZA
        $arrayConfig = DB::table('procesado_orden_config_paquete_articulo')
                ->where('id_procesado_articulo_pedido', $data['id_procesado_articulo_pedido'])
                ->where(function($query) {
                    $query->where('id_articulo', 36)
                    ->orWhere('id_articulo', 37);
                })
                ->get();
        return $arrayConfig;
    }

    public static function getPizzaBaseById($data){
        $arrayPizzaBase = DB::table('procesado_orden_config_paquete_articulo')
         ->where('id_procesado_articulo_pedido', $data['id_procesado_articulo_pedido'])
         ->where('id_articulo',$data['id_articulo']) //36=pizza base..
         ->get();
        return $arrayPizzaBase;
    }
    
    public static function getConfigPizzasOrderByExtrachesse($data){
        
         $arrayConfigPizza = DB::table('procesado_orden_config_paquete_articulo')
                ->join('procesado_orden_pizza_builder', 'procesado_orden_config_paquete_articulo.id_procesado_orden_pizza_builder', '=', 'procesado_orden_pizza_builder.id_procesado_orden_pizza_builder')
                ->join('articulo', 'procesado_orden_config_paquete_articulo.id_articulo', '=', 'articulo.id_articulo')
                ->join('tamano_articulo', 'procesado_orden_config_paquete_articulo.id_tamano_articulo', '=', 'tamano_articulo.id_tamano_articulo')
                ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
              //   ->join('rel_articulo_tamano', 'procesado_orden_config_paquete_articulo.id_tamano_articulo', '=', 'rel_articulo_tamano.id_tamano_articulo')
                ->where('procesado_orden_config_paquete_articulo.id_procesado_articulo_pedido',$data['id_procesado_articulo_pedido'])
                ->whereRaw('rel_articulo_tamano.id_tamano_articulo=procesado_orden_config_paquete_articulo.id_tamano_articulo')
                ->orderBy('procesado_orden_pizza_builder.extra_chesse','DESC')
                ->orderBy('procesado_orden_pizza_builder.extra_chesse','DESC')
                 
               ->select('procesado_orden_config_paquete_articulo.id_procesado_orden_config_paquete_articulo',
                        'procesado_orden_config_paquete_articulo.id_articulo',
                        'procesado_orden_config_paquete_articulo.id_procesado_articulo_pedido',
                        'procesado_orden_config_paquete_articulo.id_procesado_orden_pizza_builder',
                        'procesado_orden_pizza_builder.extra_chesse',
                        'procesado_orden_pizza_builder.orilla_queso',
                        'procesado_orden_config_paquete_articulo.id_tamano_articulo',
                        'tamano_articulo.app_porcion_armado_pizza',
                        'tamano_articulo.app_id_porciones_tamanno_ventamaxx',
                        'procesado_orden_config_paquete_articulo.cantidad_articulo',
                        'procesado_orden_config_paquete_articulo.id_cliente',
                        'procesado_orden_config_paquete_articulo.id_paquete_web_articulo',
                        'procesado_orden_config_paquete_articulo.config_id_esquema_cobro_ventamaxx',
                        'procesado_orden_config_paquete_articulo.config_paquete_id_tamanno_ventamaxx',
                        'procesado_orden_config_paquete_articulo.config_paquete_id_receta_ventamaxx',
                        'articulo.id_categoria',
                        'articulo.web_nombre_articulo',
                        'tamano_articulo.web_descripcion_tamano_articulo',
                        'rel_articulo_tamano.precio_articulo_tamano')
                 ->get();
                 
         return $arrayConfigPizza;
  
    }

    public static function getConfigPizzaOrderIditemsDesc($data) {


        $arrayConfigPizza = DB::table('procesado_orden_config_paquete_articulo')
                ->join('articulo', 'procesado_orden_config_paquete_articulo.id_articulo', '=', 'articulo.id_articulo')
                ->join('tamano_articulo', 'procesado_orden_config_paquete_articulo.id_tamano_articulo', '=', 'tamano_articulo.id_tamano_articulo')
                ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                //   ->join('rel_articulo_tamano', 'procesado_orden_config_paquete_articulo.id_tamano_articulo', '=', 'rel_articulo_tamano.id_tamano_articulo')
                ->where('procesado_orden_config_paquete_articulo.id_procesado_articulo_pedido', $data['id_procesado_articulo_pedido'])
                ->whereRaw('rel_articulo_tamano.id_tamano_articulo=procesado_orden_config_paquete_articulo.id_tamano_articulo')
                ->orderBy('procesado_orden_config_paquete_articulo.id_articulo', 'DESC')
                ->select('procesado_orden_config_paquete_articulo.id_procesado_orden_config_paquete_articulo', 'procesado_orden_config_paquete_articulo.id_articulo', 'procesado_orden_config_paquete_articulo.id_procesado_articulo_pedido', 'procesado_orden_config_paquete_articulo.id_procesado_orden_pizza_builder', 'procesado_orden_config_paquete_articulo.id_tamano_articulo', 'procesado_orden_config_paquete_articulo.cantidad_articulo', 'procesado_orden_config_paquete_articulo.id_cliente', 'procesado_orden_config_paquete_articulo.id_paquete_web_articulo', 'procesado_orden_config_paquete_articulo.config_id_esquema_cobro_ventamaxx', 'procesado_orden_config_paquete_articulo.config_paquete_id_tamanno_ventamaxx', 'procesado_orden_config_paquete_articulo.config_paquete_id_receta_ventamaxx', 'articulo.id_categoria', 'articulo.web_nombre_articulo', 'tamano_articulo.web_descripcion_tamano_articulo', 'rel_articulo_tamano.precio_articulo_tamano')
                ->get();

        return $arrayConfigPizza;
    }

    public static function getConfigPizzaOrderByPrice($data) {

        $arrayConfigPizza = DB::table('procesado_orden_config_paquete_articulo')
                ->join('articulo', 'procesado_orden_config_paquete_articulo.id_articulo', '=', 'articulo.id_articulo')
                ->join('tamano_articulo', 'procesado_orden_config_paquete_articulo.id_tamano_articulo', '=', 'tamano_articulo.id_tamano_articulo')
                ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                //   ->join('rel_articulo_tamano', 'procesado_orden_config_paquete_articulo.id_tamano_articulo', '=', 'rel_articulo_tamano.id_tamano_articulo')
                ->where('procesado_orden_config_paquete_articulo.id_procesado_articulo_pedido', $data['id_procesado_articulo_pedido'])
                ->whereRaw('rel_articulo_tamano.id_tamano_articulo=procesado_orden_config_paquete_articulo.id_tamano_articulo')
                ->orderBy('rel_articulo_tamano.precio_articulo_tamano', 'DESC')
                ->select('procesado_orden_config_paquete_articulo.id_procesado_orden_config_paquete_articulo',
                        'procesado_orden_config_paquete_articulo.id_articulo',
                        'procesado_orden_config_paquete_articulo.id_procesado_articulo_pedido', 
                        'procesado_orden_config_paquete_articulo.id_procesado_orden_pizza_builder',
                        'procesado_orden_config_paquete_articulo.id_tamano_articulo', 
                        'tamano_articulo.app_porcion_armado_pizza',
                        'tamano_articulo.app_id_porciones_tamanno_ventamaxx',
                        'procesado_orden_config_paquete_articulo.cantidad_articulo',
                        'procesado_orden_config_paquete_articulo.id_cliente',
                        'procesado_orden_config_paquete_articulo.id_paquete_web_articulo', 
                        'procesado_orden_config_paquete_articulo.config_id_esquema_cobro_ventamaxx',
                        'procesado_orden_config_paquete_articulo.config_paquete_id_tamanno_ventamaxx',
                        'procesado_orden_config_paquete_articulo.config_paquete_id_receta_ventamaxx', 
                        'articulo.id_categoria', 'articulo.web_nombre_articulo', 
                        'tamano_articulo.web_descripcion_tamano_articulo', 
                        'rel_articulo_tamano.precio_articulo_tamano')
                ->get();

        return $arrayConfigPizza;
    }

    public static function getOrderConfigPackageById($data){
        $arrayConfgPackage = DB::table('procesado_orden_config_paquete_articulo')
                ->join('articulo', 'procesado_orden_config_paquete_articulo.id_articulo', '=', 'articulo.id_articulo')
                ->join('tamano_articulo', 'procesado_orden_config_paquete_articulo.id_tamano_articulo', '=', 'tamano_articulo.id_tamano_articulo')
             //   ->join('rel_articulo_tamano', 'articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                //   ->join('rel_articulo_tamano', 'procesado_orden_config_paquete_articulo.id_tamano_articulo', '=', 'rel_articulo_tamano.id_tamano_articulo')
                ->where('procesado_orden_config_paquete_articulo.id_procesado_articulo_pedido', $data['id_procesado_articulo_pedido'])
                //->whereRaw('rel_articulo_tamano.id_tamano_articulo=procesado_orden_config_paquete_articulo.id_tamano_articulo')
                ->orderBy('procesado_orden_config_paquete_articulo.id_articulo', 'ASC')
                ->select(
                        'procesado_orden_config_paquete_articulo.id_articulo',
                        'procesado_orden_config_paquete_articulo.id_procesado_articulo_pedido', 
                        'procesado_orden_config_paquete_articulo.id_procesado_orden_pizza_builder',
                        'procesado_orden_config_paquete_articulo.id_tamano_articulo', 
                        'tamano_articulo.app_porcion_armado_pizza',
                        'tamano_articulo.app_id_porciones_tamanno_ventamaxx',
                        'procesado_orden_config_paquete_articulo.cantidad_articulo',
                        'procesado_orden_config_paquete_articulo.id_cliente',
                        'procesado_orden_config_paquete_articulo.id_paquete_web_articulo', 
                        'procesado_orden_config_paquete_articulo.config_id_esquema_cobro_ventamaxx',
                        'procesado_orden_config_paquete_articulo.config_paquete_id_tamanno_ventamaxx',
                        'procesado_orden_config_paquete_articulo.config_paquete_id_receta_ventamaxx', 
                        'articulo.id_categoria', 'articulo.web_nombre_articulo', 
                        'tamano_articulo.web_descripcion_tamano_articulo'
                         )
                ->get();

        return $arrayConfgPackage;
        
        /*$result =mysql_query('SELECT procesado_orden_config_paquete_articulo.id_articulo,
                        procesado_orden_config_paquete_articulo.id_procesado_articulo_pedido,
                        procesado_orden_config_paquete_articulo.id_tamano_articulo,	
                        procesado_orden_config_paquete_articulo.cantidad_articulo,
                        procesado_orden_config_paquete_articulo.id_cliente,
                         procesado_orden_config_paquete_articulo.id_paquete_web_articulo,
                        procesado_orden_config_paquete_articulo.config_id_esquema_cobro_ventamaxx,
                        procesado_orden_config_paquete_articulo.config_paquete_id_tamanno_ventamaxx,	
                        procesado_orden_config_paquete_articulo.config_paquete_id_receta_ventamaxx,
                        articulo.id_categoria,
                        articulo.web_nombre_articulo,	
                        tamano_articulo.web_descripcion_tamano_articulo	
 FROM procesado_orden_config_paquete_articulo, articulo, tamano_articulo WHERE 
         * procesado_orden_config_paquete_articulo.id_articulo=articulo.id_articulo AND
		procesado_orden_config_paquete_articulo.id_tamano_articulo=tamano_articulo.id_tamano_articulo AND 
         *    procesado_orden_config_paquete_articulo.id_procesado_articulo_pedido='.$this->id_procesado_articulo_pedido.' 
         * ORDER BY procesado_orden_config_paquete_articulo.id_articulo ASC') ;
		return $result;*/
        
        
    }
    
    
    public static function getTotalOrderConfigPackageByid($data) {

        $arrayPrice = DB::table('procesado_orden_config_paquete_articulo')
                ->join('rel_articulo_tamano', 'procesado_orden_config_paquete_articulo.id_articulo', '=', 'rel_articulo_tamano.id_articulo')
                ->whereRaw('procesado_orden_config_paquete_articulo.id_tamano_articulo=rel_articulo_tamano.id_tamano_articulo')
                ->where('procesado_orden_config_paquete_articulo.id_procesado_articulo_pedido', $data['id_procesado_articulo_pedido'])
                ->where('rel_articulo_tamano.id_unidad', $data['id_unidad'])
                //ADD FOR PRICE PER
                ->selectRaw('sum(rel_articulo_tamano.precio_articulo_tamano*procesado_orden_config_paquete_articulo.cantidad_articulo) as  precio_total_producto')
                ->get();
        return $arrayPrice[0]->precio_total_producto;
    }
    
    
    public static function getConfigBuilderByItemId($data) {

        $arrayConfigPizza = DB::table('procesado_orden_pizza_builder')
                ->where('id_procesado_articulo_pedido', $data['id_procesado_articulo_pedido'])
                ->get();

        return $arrayConfigPizza;

        /*

         * function get_procesado_orden_builder_esp_config(){
          $result=mysql_query("SELECT * FROM procesado_orden_pizza_builder WHERE id_procesado_articulo_pedido=".$this->id_procesado_articulo_pedido) ;
          return $result;
          }
         *          */
    }
    
    public static function getConfigBuilderById($data){
         $arrayConfigPizza = DB::table('procesado_orden_pizza_builder')
                ->where('id_procesado_articulo_pedido', $data['id_procesado_articulo_pedido'])
           
                ->get();

        return $arrayConfigPizza;
        
        
        
        /*function get_procesado_orden_builder_esp_config(){
		$result=mysql_query("SELECT * FROM procesado_orden_pizza_builder WHERE id_procesado_articulo_pedido=".$this->id_procesado_articulo_pedido) ;
		return $result;
	}*/
    }

    public static function getConfigBuilderByParams($data) {


        $arrayConfigPizza = DB::table('procesado_orden_pizza_builder')
                ->where('id_procesado_articulo_pedido', $data['id_procesado_articulo_pedido'])
                ->where('id_procesado_orden_pizza_builder', $data['id_procesado_orden_pizza_builder'])
                ->get();

        return $arrayConfigPizza;

        /* function get_procesado_orden_builder_esp_config_by_id(){
          $result=mysql_query("SELECT * FROM procesado_orden_pizza_builder WHERE
         *  id_procesado_articulo_pedido=".$this->id_procesado_articulo_pedido." AND 
         * id_procesado_orden_pizza_builder = ".$this->id_procesado_orden_pizza_builder);
          return $result;
          } */
    }

}
