<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
Use Illuminate\Support\Facades\DB;


class Order extends Model
{
    //

    public static function createOrder($data) {

        $idOrder = DB::table('procesado_orden_pedido')->insertGetId($data);
        return $idOrder;
       
    }
    
    public static function createItem($data){
         $idItem = DB::table('procesado_articulo_pedido')->insertGetId($data);
        return $idItem;
    }
    
    public static function createDiscount($data){
         $idItem = DB::table('procesado_orden_codigo_promocion')->insertGetId($data);
        return $idItem;
    }
    
    public static function createBasePizzaBuilder($data) {
        $idPizzaBuilder = DB::table('procesado_orden_pizza_builder')->insertGetId($data);
        return $idPizzaBuilder;
    }
    
     public static function createConfigIngredientsPizza($data) {
        $idConfigPizza = DB::table('procesado_orden_config_pizza')->insertGetId($data);
        return $idConfigPizza;
    }
    
    public static function createConfigPackageItems($data){
        
        $idProcesadoConfigPaquete=DB::table('procesado_orden_config_paquete_articulo')->insertGetId($data);
        return $idProcesadoConfigPaquete;
    }
    
    public static  function updateFolioOrderbyId($data){
        
        $affected  = DB::table('procesado_orden_pedido')
                ->where('id_procesado_orden_pedido',$data['id_procesado_orden_pedido'])
                ->update(['folio_procesado_orden_pedido' => $data['folio_procesado_orden_pedido']]);
        
        return $affected;
    }
    
    public static function updateDiscountByID($data) {

        $affected = DB::table('codigo_promocion')
                ->where('id_codigo_promocion', $data['id_codigo_promocion'])
                ->update([
            'fecha_uso_codigo_promocion' => date('Y-m-d H:i:s'),
             'id_cliente' => $data['id_cliente'],
            'id_procesado_orden_pedido' => $data['id_procesado_orden_pedido'],
            'ban_usado_codigo_promocion' => 1,
        ]);

        return $affected;
    }
    
    public static function getRowDiscountCode($data) {

        $rowCode = DB::select(DB::raw("SELECT * FROM codigo_promocion WHERE 
                    binary cadena_codigo_promocion='". $data['code']."' 
                    AND 
                    fecha_fin_vigencia_codigo_promocion >= NOW()")); 

        return $rowCode;
    }
    
    public static function getDiscountCode($data) {

        $rowCode = DB::select(DB::raw("SELECT * FROM codigo_promocion WHERE 
                    binary cadena_codigo_promocion='". $data['code']."' 
                    and ban_usado_codigo_promocion=0 AND (id_cliente=".$data['id_cliente']." OR id_cliente=0) AND 
                    fecha_fin_vigencia_codigo_promocion >= NOW()")); 

        return $rowCode;
    }
    
     public static function updateDiscountCodeByIdClient($data) {

        $affected = DB::table('codigo_promocion')
                ->where('id_codigo_promocion', $data['id_codigo_promocion'])
                ->update(['id_cliente' => $data['id_cliente']]);

        return $affected;
    }

   public static function getOrderDetailById($data){
       $order = DB::table('procesado_orden_pedido')
               ->join('unidad', 'unidad.id_unidad', '=', 'procesado_orden_pedido.id_unidad')
                ->join('cliente', 'cliente.id_cliente', '=', 'procesado_orden_pedido.id_cliente')
               ->where('id_procesado_orden_pedido',$data['id_procesado_orden_pedido'])
               ->get();
       return $order;
   }
   
   public static function getTotalOrdersPerClient($data) {
        $order = DB::table('procesado_orden_pedido')
                ->where('id_cliente', $data['id_cliente'])
                ->get();
        return $order;
    }

}
