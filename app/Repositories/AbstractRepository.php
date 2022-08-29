<?php

    namespace App\Repositories;

    use Illuminate\Database\Eloquent\Model;

    abstract class AbstractRepository {
        public function __construct(Model $model, $tipo)
        {
            $this->tipo = $tipo;
            $this->model = $model;
        }

        public function selectAtributosSelecionados($atributos) {
            if(!is_null($this->tipo)) {
                $this->model = $this->model->selectRaw($atributos)->with($this->tipo)->get();
            }else {
                $this->model = $this->model->selectRaw($atributos)->get();
            }
            
        }

        public function selectTodosAtributos() {
            if(!is_null($this->tipo)) {
                $this->model = $this->model->with($this->tipo)->get();
            }else {
                $this->model = $this->model->get();
            }
        }

        public function filtrarQuery($filtro) {
            $filtros = explode(';', $filtro);
            foreach($filtros as $filtro) {
                $condicoes = explode(':', $filtro);
                $this->model = $this->model->where($condicoes[0], $condicoes[1], $condicoes[2]);
            }
        }

        public function getResultado() {
            return $this->model->all();
        }
    }

?>