<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
Use Illuminate\Support\Facades\DB;

class Size extends Model
{
    //
    protected $table = 'tamano_articulo';
    
     public static function getRelImgBySizeId($data) {
      $rowImgSizes = DB::table('app_rel_img_bases_tamano_pizza')
                ->where('id_tamano_articulo', $data['id_tamano_articulo'])
              //  ->orderBy('id_tamano_articulo', 'asc')
                ->get(); //vigente..
        
        return $rowImgSizes;
    }

    
}
