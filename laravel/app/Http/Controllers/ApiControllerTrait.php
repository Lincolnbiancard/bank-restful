<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

trait ApiControllerTrait 
{
    
    public function index(Request $request)
    {
        $limit = $request->all()['limit'] ?? 20;

        $order = $request->all()['order'] ?? null;

        if($order !== null) {
            $order = explode(',', $order);
        }

        $order[0] = $order[0] ?? 'id';
        $order[1] = $order[1] ?? 'asc';

        $where  = $request->all()['where'] ?? [];

        $like   = $request->all()['like'] ?? null;
        if($like) {
            $like       = explode(',', $like);
            $like[1]    = '%' . $like[1] . '%';
        }

        $result = $this->model->orderBy($order[0], $order[1])
            ->where(function($query) use ($like) {
                if($like){
                    return $query->where($like[0], 'like', $like[1]);
                }
                return $query;
            })
            ->where($where)
            ->with($this->relationships()) //traz relacionamentos na listagem
            ->paginate($limit);

        return response()->json($result);
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $result = $this->model->create($request->all());
        return response()->json($result);
    }
   
    public function show($id)
    {
        $result = $this->model->with($this->relationships())->findOrFail($id);
        return response()->json($result);
    }
  
    public function edit($id)
    {
        //
    }
  
    public function update(Request $request, $id)
    {
        $result = $this->model->findOrFail($id);
        $result->update($request->all());
        return response()->json($result);
    }

   
    public function destroy($id)
    {
        $result = $this->model->findOrFail($id);
        $result->delete();
        return response()->json($result);
    }

    protected function relationships()
    {
        if(isset($this->relationships)) {
            return $this->relationships;
        }
        return [];
    }
}
