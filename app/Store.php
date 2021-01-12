<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
Use Illuminate\Support\Facades\DB;


class Store extends Model
{
    //
    protected $table = 'unidad';
    
    
    
     public static function getOpeninHourByParams($data) {
        $row = DB::table('rel_horario_dia_unidad')
                        ->where('id_unidad', $data['id_unidad'])
                        ->where('id_dia_semana', $data['id_dia_semana'])->get();

        return $row;
    }
    
    public static function getConfigRecordOpeningHour() {
        $row = DB::table('config')
                        ->where('id_config',12)
                        ->get();

        return $row;
    }
    
    public static function getStatusTaxStore($data) {
        $row = DB::table('unidad')
                ->where('id_unidad', $data['id_unidad'])
                ->get();

        return $row[0]->cobra_impuesto_iva;
    }
    //DISCONUNTS PER PRODUCTS..
    public static function getDSCstatus($data) {
        $row = DB::table('config')
                ->where('id_config', 14)
                ->get();

        return $row;
    }
    
    public static function getLoyaltyStatus($data) {
        $row = DB::table('config')
                ->where('id_config', 15)
                ->get();

        return $row;
    }
    

}
