<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    protected Modelo $modelo;

    public function __construct(Modelo $modelo)
    {   
        // instanciando model(via injeção de dependecias)
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo, 'marca');
        
        if($request->has('atributos')) {
            $modeloRepository->selectAtributosSelecionados($request->get('atributos'));
        }else {
            $modeloRepository->selectTodosAtributos();
        }

        if($request->has('filtro')) {
            $modeloRepository->filtrarQuery($request->filtro);
        }

        return response()->json($modeloRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());
        // stateless (cade requisição é unica)

        $imagem = $request->file('imagem');
        $nomeImagem = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'nome' => $request->nome,
            'imagem' => $nomeImagem,
            'lugares' => $request->lugares,
            'numero_portas' => $request->numero_portas,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
            'marca_id' => $request->marca_id
        ]);
        
        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id);

        if(is_null($modelo)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return $modelo;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);

        if(is_null($modelo)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }

        $request->validate($modelo->rules());

        if($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $nomeImagem = $imagem->store('imagens/modelo', 'public');

        $modelo->update([
            'nome' => $request->nome,
            'imagem' => $nomeImagem,
            'lugares' => $request->lugares,
            'numero_portas' => $request->numero_portas,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
            'marca_id' => $request->marca_id
        ]);

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);
        if(is_null($modelo)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }

        Storage::disk('public')->delete($modelo->imagem);
        
        return $modelo->delete();
    }
}