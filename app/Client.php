<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
Use Illuminate\Support\Facades\DB;

class Client extends Model {
    //
    protected $table = 'cliente';

    public static function getClientById($data) {
        $rowProduct = DB::table('cliente')
                ->join('colonias_garantia_ube', 'cliente.id_colonias_garantia_ube', '=', 'colonias_garantia_ube.id_colonias_garantia_ube')
                ->join('estado_republica', 'colonias_garantia_ube.id_estado_republica', '=', 'estado_republica.id_estado_republica')
                ->join('ciudad_republica', 'colonias_garantia_ube.id_ciudad_republica', '=', 'ciudad_republica.id_ciudad_republica')
                ->where('cliente.id_cliente', $data['id_cliente'])
                //  ->where('cliente.vigencia_cliente', 1)
                ->get(); //vigente..

        return $rowProduct;
    }

    public static function getClientNoAddressById($data) {
        $rowProduct = DB::table('cliente')
                //  ->join('colonias_garantia_ube', 'cliente.id_colonias_garantia_ube', '=', 'colonias_garantia_ube.id_colonias_garantia_ube')
                //  ->join('estado_republica', 'colonias_garantia_ube.id_estado_republica', '=', 'estado_republica.id_estado_republica')
                //  ->join('ciudad_republica', 'colonias_garantia_ube.id_ciudad_republica', '=', 'ciudad_republica.id_ciudad_republica')
                ->where('cliente.id_cliente', $data['id_cliente'])
                //  ->where('cliente.vigencia_cliente', 1)
                ->get(); //vigente..

        return $rowProduct;
    }

    public static function getClientByEmail($data) {
        $rowProduct = DB::table('cliente')
                ->join('colonias_garantia_ube', 'cliente.id_colonias_garantia_ube', '=', 'colonias_garantia_ube.id_colonias_garantia_ube')
                ->join('estado_republica', 'colonias_garantia_ube.id_estado_republica', '=', 'estado_republica.id_estado_republica')
                ->join('ciudad_republica', 'colonias_garantia_ube.id_ciudad_republica', '=', 'ciudad_republica.id_ciudad_republica')
                ->where('cliente.email_cliente', $data['email_cliente'])
                ->get(); //vigente..

        return $rowProduct;
    }
    
     public static function getClientNoAddressByEmail($data) {
        $rowProduct = DB::table('cliente')
               // ->join('colonias_garantia_ube', 'cliente.id_colonias_garantia_ube', '=', 'colonias_garantia_ube.id_colonias_garantia_ube')
              //  ->join('estado_republica', 'colonias_garantia_ube.id_estado_republica', '=', 'estado_republica.id_estado_republica')
              //  ->join('ciudad_republica', 'colonias_garantia_ube.id_ciudad_republica', '=', 'ciudad_republica.id_ciudad_republica')
                ->where('cliente.email_cliente', $data['email_cliente'])
                ->get(); //vigente..

        return $rowProduct;
    }

    public static function getActiveRelAddressByClientId($data) {
        $addressClient = DB::table('rel_domicilio_cliente')
                        //   ->join('cliente', 'rel_domicilio_cliente.id_cliente', '=', 'cliente.id_cliente')
                        ->join('unidad', 'rel_domicilio_cliente.sucursal_id_unidad', '=', 'unidad.id_unidad')
                        ->join('colonias_garantia_ube', 'rel_domicilio_cliente.id_colonias_garantia_ube', '=', 'colonias_garantia_ube.id_colonias_garantia_ube')
                        ->join('estado_republica', 'colonias_garantia_ube.id_estado_republica', '=', 'estado_republica.id_estado_republica')
                        ->join('ciudad_republica', 'colonias_garantia_ube.id_ciudad_republica', '=', 'ciudad_republica.id_ciudad_republica')
                        ->where('rel_domicilio_cliente.id_cliente', $data['id_cliente'])
                        ->where('rel_domicilio_cliente.vigencia_rel_domicilio_cliente', 1)
                        ->where('rel_domicilio_cliente.ban_default_domicilio_cliente', 1)->get();

        //dd($recordClient);
        return $addressClient;
    }

    public static function getAddressById($data) {
        $addressClient = DB::table('rel_domicilio_cliente')
                //   ->join('cliente', 'rel_domicilio_cliente.id_cliente', '=', 'cliente.id_cliente')
                ->join('unidad', 'rel_domicilio_cliente.sucursal_id_unidad', '=', 'unidad.id_unidad')
                ->join('colonias_garantia_ube', 'rel_domicilio_cliente.id_colonias_garantia_ube', '=', 'colonias_garantia_ube.id_colonias_garantia_ube')
                ->join('estado_republica', 'colonias_garantia_ube.id_estado_republica', '=', 'estado_republica.id_estado_republica')
                ->join('ciudad_republica', 'colonias_garantia_ube.id_ciudad_republica', '=', 'ciudad_republica.id_ciudad_republica')
                ->where('rel_domicilio_cliente.id_rel_domicilio_cliente', $data['id_rel_domicilio_cliente'])
                ->where('rel_domicilio_cliente.id_cliente', $data['id_cliente'])
                ->get();

        //dd($recordClient);
        return $addressClient;
    }

    public static function getNoAddressById($data) {

        $addressClient = DB::table('rel_domicilio_cliente')
                //   ->join('cliente', 'rel_domicilio_cliente.id_cliente', '=', 'cliente.id_cliente')
                //  ->join('unidad', 'rel_domicilio_cliente.sucursal_id_unidad', '=', 'unidad.id_unidad')
                //  ->join('colonias_garantia_ube', 'rel_domicilio_cliente.id_colonias_garantia_ube', '=', 'colonias_garantia_ube.id_colonias_garantia_ube')
                //  ->join('estado_republica', 'colonias_garantia_ube.id_estado_republica', '=', 'estado_republica.id_estado_republica')
                //   ->join('ciudad_republica', 'colonias_garantia_ube.id_ciudad_republica', '=', 'ciudad_republica.id_ciudad_republica')
                ->where('rel_domicilio_cliente.id_rel_domicilio_cliente', $data['id_rel_domicilio_cliente'])
                ->where('rel_domicilio_cliente.id_cliente', $data['id_cliente'])
                ->get();
        return $addressClient;
    }

    public static function getAllRelAddressClientById($data) {
        $addressClient = DB::table('rel_domicilio_cliente')
                //   ->join('cliente', 'rel_domicilio_cliente.id_cliente', '=', 'cliente.id_cliente')
              //  ->join('unidad', 'rel_domicilio_cliente.sucursal_id_unidad', '=', 'unidad.id_unidad')
              //  ->join('colonias_garantia_ube', 'rel_domicilio_cliente.id_colonias_garantia_ube', '=', 'colonias_garantia_ube.id_colonias_garantia_ube')
              //  ->join('estado_republica', 'colonias_garantia_ube.id_estado_republica', '=', 'estado_republica.id_estado_republica')
             //   ->join('ciudad_republica', 'colonias_garantia_ube.id_ciudad_republica', '=', 'ciudad_republica.id_ciudad_republica')
                ->where('rel_domicilio_cliente.id_cliente', $data['id_cliente'])
                ->where('rel_domicilio_cliente.vigencia_rel_domicilio_cliente', 1)
                ->get();
        return $addressClient;
    }
    
    public static function getColonyById($data){
        $addressClient=DB::table('colonias_garantia_ube')
                 ->join('estado_republica', 'colonias_garantia_ube.id_estado_republica', '=', 'estado_republica.id_estado_republica')
                ->join('ciudad_republica', 'colonias_garantia_ube.id_ciudad_republica', '=', 'ciudad_republica.id_ciudad_republica')
                ->where('colonias_garantia_ube.id_colonias_garantia_ube', $data['id_colonias_garantia_ube'])
                ->get();
                return $addressClient;
    }
    
    public static function enableDefaultRelAddressClient($data) {
        $updateAddress = DB::table('rel_domicilio_cliente')
                ->where('id_rel_domicilio_cliente', $data['id_rel_domicilio_cliente'])
                ->update(['ban_default_domicilio_cliente' => 1]);

        return $updateAddress;
    }

    public static function disableAllRelAddressClient($data) {

        $updateAddress = DB::table('rel_domicilio_cliente')
                ->where('id_cliente', $data['id_cliente'])
                ->update(['ban_default_domicilio_cliente' => 0]);

        return $updateAddress;
    }

    public static function deleteAddressClientById($data) {

        $updateAddress = DB::table('rel_domicilio_cliente')
                ->where('id_rel_domicilio_cliente', $data['id_rel_domicilio_cliente'])
                ->where('id_cliente', $data['id_cliente'])
                ->update(['vigencia_rel_domicilio_cliente' => 0]);

        return $updateAddress;
    }

    public static function disablePrincipalAddressClient($data) {
        $updateAddress = DB::table('cliente')
                ->where('id_cliente', $data['id_cliente'])
                ->update(['ban_default_domicilio_cliente' => 0]);

        return $updateAddress;
    }

    public static function enablePrincipalAddressClient($data) {
        $updateAddress = DB::table('cliente')
                ->where('id_cliente', $data['id_cliente'])
                ->update(['ban_default_domicilio_cliente' => 1]);

        return $updateAddress;
    }

    public static function editRelAddressById($data) {
        $updateAddress = DB::table('rel_domicilio_cliente')
                ->where('id_rel_domicilio_cliente', $data['id_rel_domicilio_cliente'])
                ->where('id_cliente', $data['id_cliente'])
                ->update($data['array_values']);

        return $updateAddress;
    }

    public static function editClientById($data) {
        $updateAddress = DB::table('cliente')
                ->where('id_cliente', $data['id_cliente'])
                ->update($data['data_update']);
        return $updateAddress;
    }

    public static function addRelAddressClient($array) {

        $insertAddrees = DB::table('rel_domicilio_cliente')->insertGetId($array);
        return $insertAddrees;
    }

}
