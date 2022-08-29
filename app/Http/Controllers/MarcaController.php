<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    protected Marca $marca;

    public function __construct(Marca $marca)
    {   
        // instanciando model(via injeção de dependecias)
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca, 'modelos');
        
        if($request->has('atributos')) {
            $marcaRepository->selectAtributosSelecionados($request->get('atributos'));
        }else {
            $marcaRepository->selectTodosAtributos();
        }

        if($request->has('filtro')) {
            $marcaRepository->filtrarQuery($request->filtro);
        }

        return response()->json($marcaRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->marca->rules(), $this->marca->feedback());
        // stateless (cade requisição é unica)

        $imagem = $request->file('imagem');
        $nomeImagem = $imagem->store('imagens', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $nomeImagem
        ]);
        
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);

        if(is_null($marca)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return $marca;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);
        if(is_null($marca)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        $request->validate($marca->rules(), $marca->feedback());
        return $marca->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);
        if(is_null($marca)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return $marca->delete();
    }
}
