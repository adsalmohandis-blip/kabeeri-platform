<?php

namespace App\Filament\Resources\DataIngestionPipelines\Pages;

use App\Filament\Resources\DataIngestionPipelines\DataIngestionPipelineResource;
use Filament\Resources\Pages\ListRecords;

class ListDataIngestionPipelines extends ListRecords
{
    protected static string $resource = DataIngestionPipelineResource::class;
}
