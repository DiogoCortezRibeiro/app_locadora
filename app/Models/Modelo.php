<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'imagem', 'numero_portas', 'lugares', 'marca_id', 'air_bag', 'abs'];

    public function rules() {
        return [
            'nome'   => 'required|unique:modelos,nome,'.$this->id.'',
            'imagem' => 'required|file|mimes:png,jpge,jpg',
            'marca_id' => 'exists:marcas,id',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean'
        ];
    }

    public function marca() {
        // Um modelo Pertence a UMA marca
        return $this->belongsTo('App\Models\Marca');
    }
}
