<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Comentario;

class ComentarioController extends Controller
{
    protected $fields = [
	'phone_id', 'empresa_id', 'estado', 'like', 'comentario', 'tipo' 
     ];

    protected $rules = [
        'phone_id' => 'required', 'empresa_id' => 'required', 
        'estado' => 'required',  'comentario' => ''
    ];
    
    public function all(){
        return response()->json(['data' => Comentario::all()]);
    }
    
    public function get(Request $request){
        $params = $request->request->all();
        $whereRaw = array_key_exists('where_raw', $params) ? $params['where_raw'] : false;
        unset($params['api_token']);
        unset($params['where_raw']);
        if(!$whereRaw){
            $res = Comentario::where($params)->get();
        }
        else{
            $res = Comentario::whereRaw($whereRaw)->get();
        }
        return response()->json(['data' => $res]);
    }
    
    public function find($id){
        $model = Comentario::find($id);
        if($model){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json([], 422);
        }
    }
    
    public function new(Request $request){
        return $this->save($request, new Comentario, $this->rules, $this->fields);
    }
    
    public function put(Request $request, $id){
        $rules = $this->rules;
        $fields = $this->fields;
        return $this->save($request, Comentario::find($id), $rules, $fields);
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
        return $this->save($request, Comentario::find($id), $rules, $fields);
    }
    
    public function delete(Request $request, $id){
        $res = Comentario::destroy($id);
        if($res){
            return response()->json(['data' => $model]);
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
        $res = $model->save();
        if($res){
            return response()->json(['data' => $model]);
        }
        else{
            return response()->json(['data' => $res], 422);
        }
    }
}