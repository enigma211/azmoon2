<?php

namespace App\Filament\Resources\QuestionReportResource\Pages;

use App\Filament\Resources\QuestionReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuestionReports extends ListRecords
{
    protected static string $resource = QuestionReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Remove create action - reports are only created by users
        ];
    }
}
