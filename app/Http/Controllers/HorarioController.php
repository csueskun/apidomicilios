<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Horario;

class HorarioController extends Controller
{
    protected $fields = [
        'day','hora_inicio','hora_fin','des', 'empresa_id'
		];

    protected $rules = [];

    protected $days = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    
    public function all(){
        return response()->json(['data' => Horario::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        $res = new Horario;
        if(array_key_exists('order_desc', $params)){
            $res = $res->orderBy($params['order_desc'], 'desc');
        }
        elseif(array_key_exists('order_asc', $params)){
            $res = $res->orderBy($params['order_asc']);
        }
        unset($params['order_desc']);
        unset($params['order_asc']);
        if(!$whereRaw){
            $res = $res->where($params);
        }
        else{
            $res = $res->whereRaw($whereRaw);
        }
        $res = $res->get();
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = Horario::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Horario, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $fields = $this->fields;
        return $this->save($request, Horario::find($id), $rules, $fields);
    }
    
    public function patch(Request $request, $id){
        $rules = $this->rules;
        $fields = $this->fields;
        foreach ($rules as $key => $value) {
            if(!$request->has($key)){
                unset($rules[$key]);
            }
        }
        $fieldCount = count($fields);
        for ($i=0; $i < $fieldCount ;$i++) { 
            if(!$request->has($fields[$i])){
                unset($fields[$i]);
            }
        }
        return $this->save($request, Horario::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Horario::destroy($id);
        if($res){
            return response()->json([]);
        }
        else{
            return response()->json(['data' => $res], 422);
        }
    }

    public function save($request, $model, $rules, $fields){
        $this->validate($request, $rules);
        foreach($fields as $field){
            $model->$field = $request->input($field);
        }
        $model->day = intval($model->day);
        if($model->day>7){
            $end = intval($model->day) - 3;
            for ($i=1; $i <= $end; $i++){
                $model_ = new Horario;
                $model_->day = $i;
                $model_->hora_inicio = $model->hora_inicio;
                $model_->hora_fin = $model->hora_fin;
                $model_->hora_fin = $model->hora_fin;
                $model_->des = $this->days[$i].' '.$model->hora_inicio.' - '.$model->hora_fin;
                $model_->empresa_id = $model->empresa_id;
                $res = $model_->save();
            }
        }
        else{
            $model->des = $this->days[$model->day].' '.$model->hora_inicio.' - '.$model->hora_fin;
            $res = $model->save();
        }
        if($res){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json(['data' => $res], 422);
        }
    }
}