<?php

namespace App\Http\Controllers;

use App\Models\Locacao;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{
    protected Locacao $locacao;

    public function __construct(Locacao $locacao)
    {   
        // instanciando model(via injeção de dependecias)
        $this->locacao = $locacao;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $locacaoRepository = new LocacaoRepository($this->locacao, 'cliente');
        
        if($request->has('atributos')) {
            $locacaoRepository->selectAtributosSelecionados($request->get('atributos'));
        }else {
            $locacaoRepository->selectTodosAtributos();
        }

        if($request->has('filtro')) {
            $locacaoRepository->filtrarQuery($request->filtro);
        }

        return response()->json($locacaoRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLocacaoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->locacao->rules());
        // stateless (cade requisição é unica)

        $locacao = $this->locacao->create([
            'cliente_id' => $request->cliente_id,
            'carro_id' => $request->carro_id,
            'data_inicio_periodo' => $request->data_inicio_periodo,
            'data_final_previsto_periodo' => $request->data_final_previsto_periodo,
            'data_final_realizado_periodo' => $request->data_final_realizado_periodo,
            'valor_diaria' => $request->valor_diaria,
            'km_inicial' => $request->km_inicial,
            'km_final' => $request->km_final
        ]);
        
        return response()->json($locacao, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $locacao = $this->locacao->with('cliente')->find($id);

        if(is_null($locacao)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return $locacao;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLocacaoRequest  $request
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $locacao = $this->locacao->find($id);
        if(is_null($locacao)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        $request->validate($locacao->rules());
        return $locacao->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $locacao = $this->locacao->find($id);
        if(is_null($locacao)) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return $locacao->delete();
    }
}
