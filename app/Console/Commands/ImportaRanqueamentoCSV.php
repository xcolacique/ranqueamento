<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
//use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class ImportaRanqueamentoCSV extends Command
{
    protected $signature = 'csv:importar {directory}';
    protected $description = 'Importa arquivos .CSV de ranqueamentos anteriores';

    public function handle()
    {
       $diretorio = $this->argument('directory');

       // Apenas nome
       $nomesCSV = collect(File::files($diretorio))
            ->filter(fn($arquivo) => $arquivo->getExtension() === 'csv')
            ->map(fn($arquivo) => $arquivo->getFilename());

        // Caminho completo
        $arquivosCSV = collect(File::files($diretorio))
            ->filter(fn($arquivo) => $arquivo->getExtension() === 'csv')
            ->map(fn($arquivo) => $arquivo->getPathname());

       // $this->info("Arquivos que serão inseridos:");
       // $this->line($arquivosCSV->implode("\n")); 
       // $this->line($nomesCSV->implode("\n")); 

        // Pegar o primeiro arquivo pra testar
        $primeiroArquivo = $arquivosCSV->first();

    if ($primeiroArquivo) {
    $this->info("Arquivo: {$primeiroArquivo}\n");
    
    $csv = Reader::createFromPath($primeiroArquivo, 'r');
    $csv->setHeaderOffset(0);
    
    $registros = collect($csv->getRecords())->toArray();
    
    /*if (!empty($registros)) {
        $this->table(
            array_keys($registros[0]),
            $registros
        );
    }*/
}


    }
}
