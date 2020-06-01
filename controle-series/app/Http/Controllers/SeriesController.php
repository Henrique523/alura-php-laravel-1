<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeriesFormRequest;
use App\Serie;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index(Request $request) {
        $series = Serie::query()->OrderBy('nome')->get();
        $mensagem = $request->session()->get('mensagem');

        return view('series.index', compact('series', 'mensagem'));
    }

    public function create()
    {
        return view('series.create');
    }

    public function store(SeriesFormRequest $request)
    {
        $request->validate();
        $serie = Serie::create(['nome' => $request->nome]);
        $qtdTemporadas = $request->qtd_temporadas;

        for($i = 1; $i <= $qtdTemporadas; $i++) {
            $temporada = $serie->temporadas()->create(['numero' => $i]);

            for($j = 1; $j <= $request->ep_por_temporada; $j++) {
                $episodio = $temporada->episodios()->create(['numero' => $j]);
            }
        }

        $request->session()->flash(
            'mensagem',
            "Série {$serie->id} e suas temporadas e episódios criados com sucesso: {$serie->nome}"
        );

        return redirect()->route('listar_series');
    }

    public function destroy(Request $request)
    {
        $request->session()->flash(
            'mensagem',
            "Série removida com sucesso"
        );
        Serie::destroy($request->id);

        return redirect()->route('listar_series');
    }
}
