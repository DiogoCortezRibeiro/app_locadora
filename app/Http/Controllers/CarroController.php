<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    protected Carro $carro;

    public function __construct(Carro $carro)
    {   
        // instanciando model(via injeção de dependecias)
        $this->carro = $carro;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carroRepository = new CarroRepository($this->carro, 'modelo');
        
        if($request->has('atributos')) {
            $carroRepository->selectAtributosSelecionados($request->get('atributos'));
        }else {
            $carroRepository->selectTodosAtributos();
        }

        if($request->has('filtro')) {
            $carroRepository->filtrarQuery($request->filtro);
        }

        return response()->json($carroRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules());
        // stateless (cade requisição é unica)

        $carro = $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);
        
        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $carro = $this->carro->with('modelo')->find($id);

        if(is_null($carro)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return $carro;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $carro = $this->carro->find($id);
        if(is_null($carro)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        $request->validate($carro->rules());
        return $carro->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carro = $this->carro->find($id);
        if(is_null($carro)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return $carro->delete();
    }
}
