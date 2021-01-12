<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
Use Illuminate\Support\Facades\DB;

class TrackingOrder extends Model
{
    //
    public static function getAdminByEmail($data) {
        $rowItem = DB::table('app_admin_user')
                ->where('email_admin_user', $data['email_admin_user'])
                ->where('vigencia_admin_user', 1)
                ->get(); //vigente..
        return $rowItem;
    }
    
    public static function getAdminStoresById($data) {
        $rowItem = DB::table('app_admin_rel_user_store')
                ->join('unidad', 'unidad.id_unidad', '=', 'app_admin_rel_user_store.id_unidad')
              //  ->join('app_admin_user', 'app_admin_user.id_app_admin_user', '=', 'app_admin_rel_user_store.id_app_admin_user')
                ->where('app_admin_rel_user_store.id_app_admin_user', $data['id_app_admin_user'])
                ->where('app_admin_rel_user_store.vigencia_app_admin_rel_user_store', 1)
                ->orderBy('app_admin_rel_user_store.id_unidad', 'ASC')
                ->get(); //vigente..
        return $rowItem;
    }
    
    public static function getAdminStoreById($data) {
        $rowItem = DB::table('unidad')
                ->where('id_unidad', 1)
                ->get(); //vigente..
        return $rowItem;
    }

}
