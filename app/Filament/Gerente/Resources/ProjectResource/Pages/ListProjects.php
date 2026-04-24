<?php

namespace App\Filament\Gerente\Resources\ProjectResource\Pages;

use App\Filament\Gerente\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make(__('resources.project.tab_all'))
                ->badge($this->orderByStatus() ?? 0)
                ->badgeColor(Color::Amber),
            Tab::make(__('resources.project.tab_pending'))
                ->query(fn ($query) => $query->where('project_status', 'pending'))
                ->badge($this->orderByStatus('pending') ?? 0)
                ->badgeColor(Color::Orange),
            Tab::make(__('resources.project.tab_in_progress'))
                ->query(fn ($query) => $query->where('project_status', 'in_progress'))
                ->badge($this->orderByStatus('in_progress') ?? 0)
                ->badgeColor(Color::Indigo),
            Tab::make(__('resources.project.tab_finished'))
                ->query(fn ($query) => $query->where('project_status', 'finished'))
                ->badge($this->orderByStatus('finished') ?? 0)
                ->badgeColor(Color::Green),
            Tab::make(__('resources.project.tab_stopped'))
                ->query(fn ($query) => $query->where('project_status', 'stopped'))
                ->badge($this->orderByStatus('stopped') ?? 0)
                ->badgeColor(Color::Red),
        ];
    }

    public function orderByStatus(?string $status = null) {
        if(blank($status)) {
            return Project::count();
        }
        return Project::where('project_status', $status)->count();
    }
}
