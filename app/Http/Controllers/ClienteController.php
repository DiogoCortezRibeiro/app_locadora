<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected Cliente $carro;

    public function __construct(Cliente $cliente)
    {   
        // instanciando model(via injeção de dependecias)
        $this->cliente = $cliente;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clienteRepository = new ClienteRepository($this->cliente, null);
        
        if($request->has('atributos')) {
            $clienteRepository->selectAtributosSelecionados($request->get('atributos'));
        }else {
            $clienteRepository->selectTodosAtributos();
        }

        if($request->has('filtro')) {
            $clienteRepository->filtrarQuery($request->filtro);
        }

        return response()->json($clienteRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->cliente->rules());
        // stateless (cade requisição é unica)

        $cliente = $this->cliente->create([
            'nome' => $request->nome
        ]);
        
        return response()->json($cliente, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cliente = $this->cliente->find($id);

        if(is_null($cliente)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return $cliente;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cliente = $this->cliente->find($id);
        if(is_null($cliente)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        $request->validate($cliente->rules());
        return $cliente->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = $this->cliente->find($id);
        if(is_null($cliente)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return $cliente->delete();
    }
}
