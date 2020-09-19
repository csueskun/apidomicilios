<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Pedido;
use App\PedidoDetalle;

class PedidoController extends Controller
{
    public function full(Request $request){
        $pedido = new Pedido;
        $pedido->phone_id = $request->input('phone_id');
        $pedido->empresa_id = $request->input('empresa_id');
        $pedido->total = 0;

        $items = $request->input('items');
        try {
            foreach($items as $item){
                $pedido->total += $item['cantidad'] * $item['precio'];
            }
        } catch (\Throwable $th) {
            
        }
        $pedido->save();

        if(!$pedido){
            return response()->json([], 422);
        }
        foreach($items as $item){
            $detalle = new PedidoDetalle;
            $detalle->pedido_id = $pedido->id;
            $detalle->producto_id = $item['id'];
            $detalle->cantidad = $item['cantidad'];
            $detalle->valorunitario = $item['precio'];
            $detalle->totalparcial = floatval($detalle->cantidad) * floatval($detalle->valorunitario);
            $detalle->save();
        }

        return response()->json([], 200);

    }
}