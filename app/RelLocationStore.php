<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
Use Illuminate\Support\Facades\DB;

class RelLocationStore extends Model
{
    //
    
     protected $table = 'colonias_garantia_ube';
     
     public static function getLocationStores() {
        $rowProduct = DB::table('colonias_garantia_ube')
                         ->join('status_garantia_ube', 'status_garantia_ube.id_status_garantia_ube', '=', 'colonias_garantia_ube.id_status_garantia_ube')
                        ->orderBy('colonias_garantia_ube.id_colonias_garantia_ube','asc')
                        ->get(); //vigente..

        return $rowProduct;
    }
    
    public static function getLocationStoresById($data) {
        $rowProduct = DB::table('colonias_garantia_ube')
                         ->join('status_garantia_ube', 'status_garantia_ube.id_status_garantia_ube', '=', 'colonias_garantia_ube.id_status_garantia_ube')
                        ->where('colonias_garantia_ube.id_colonias_garantia_ube',$data['id_colonias_garantia_ube'])
                        ->get(); //vigente..

        return $rowProduct;
    }
     
}
