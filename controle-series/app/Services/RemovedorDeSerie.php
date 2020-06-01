<?php

namespace App\Services;

use App\{Episodio, Serie, Temporada};
use Illuminate\Support\Facades\DB;

class RemovedorDeSerie
{
    public function removerSerie(int $serieId): string
    {
        $nomeSerie = '';
        DB::transaction(function () use ($serieId, &$nomeSerie)  {
            $serie = Serie::find($serieId);
            $nomeSerie = $serie->nome;
            $this->removerTemporada($serie);
            $serie->delete();
        });

        return $nomeSerie;
    }

    /**
     * @param $serie
     */
    private function removerTemporada(Serie $serie): void
    {
        $serie->temporadas->each(function (Temporada $temporada) {
            $this->removerEpisodio($temporada);
            $temporada->delete();
        });

    }

    /**
     * @param Temporada $temporada
     * @throws \Exception
     */
    private function removerEpisodio(Temporada $temporada): void
    {
        $temporada->episodios->each(function (Episodio $episodio) {
            $episodio->delete();
        });
    }
}
