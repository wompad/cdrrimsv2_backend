<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ListModels extends Command
{
    protected $signature = 'list:models';
    protected $description = 'List all models in the app/Models directory';

    public function handle()
    {
        $modelsPath = app_path('Models');
        $models = File::files($modelsPath);

        foreach ($models as $model) {
            $this->info($model->getFilename());
        }
    }
}

