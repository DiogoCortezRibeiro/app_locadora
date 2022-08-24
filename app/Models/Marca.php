<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];

    public function rules() {
        return [
            'nome'   => 'required|unique:marcas,nome,'.$this->id.'',
            'imagem' => 'required|file|mimes:png'
        ];

        /* validação unique tem tres parametros (tabela, nome da coluna que será pesquisada na tabela, id do registro que será desconsiderado na pesquisa) */
    }

    public function feedback() {
        return [
            'required' => 'O campo :attribute é obrigatorio',
            'nome.unique' => 'O nome da marca já existe',
            'imagem.mimes' => 'O arquivo deve ser uma imagemdo tipo PNG'
        ];
    }

    public function modelos() {
        // Uma marca Possui muitos modelos
        return $this->hasMany('App\Models\Modelo');
    }
}
