<?php

namespace App\Filament\Gerente\Resources\PropertyAssignmentResource\Pages;

use App\Exports\PropertiesPerUserExport;
use App\Filament\Gerente\Resources\PropertyAssignmentResource;
use App\Models\PropertyAssignment;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Maatwebsite\Excel\Facades\Excel;

class ListPropertyAssignments extends ListRecords
{
    protected static string $resource = PropertyAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('exportar_reporte')
                ->label('Exportar reporte')
                ->color('success')
                ->icon('heroicon-m-arrow-down-tray')
                ->form([
                    Grid::make(3)->schema([
                        Select::make('month')
                        ->label('Mes')
                        ->options(collect(range(1, 12))->mapWithKeys(fn($m) => [$m => Carbon::create()->month($m)->locale('es')->translatedFormat('F')]))
                        ->default(now()->month)
                        ->required()
                        ->reactive(),

                        Select::make('year')
                            ->label('Año')
                            ->options(fn() => collect(range(now()->year - 1, now()->year))->reverse()->mapWithKeys(fn($y) => [$y => $y]))
                            ->default(now()->year)
                            ->required(),

                        Select::make('week')
                            ->label('Semana (opcional)')
                            ->options(function (Get $get) {
                                $month = $get('month');
                                $weeks = [];

                                if ($month) {
                                    $start = Carbon::create(now()->year, $month)->startOfMonth();
                                    $end = $start->copy()->endOfMonth();

                                    while ($start->lte($end)) {
                                        $weekNumber = $start->isoWeek();
                                        $weeks[$weekNumber] = 'Semana ' . $weekNumber;
                                        $start->addDay();
                                    }
                                }

                                return $weeks;
                            })
                    ])
                ])
                ->action(function (array $data) {
                    return Excel::download(
                        new PropertiesPerUserExport($data['month'], $data['year'], $data['week']),
                        'reporte-solicitudes-de-propiedades-por-usuario.xlsx'
                    );
                })
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make(__('resources.property_assignment.tab_total_property_assignments'))
                ->badge($this->orderByAssignmentStatus() ?? 0)
                ->badgeColor(Color::Amber),
            Tab::make(__('resources.property_assignment.tab_pending'))
                ->badge($this->orderByAssignmentStatus('pending') ?? 0)
                ->badgeColor(Color::Orange),
            Tab::make(__('resources.property_assignment.tab_received'))
                ->badge($this->orderByAssignmentStatus('received') ?? 0)
                ->badgeColor(Color::Gray),
            Tab::make(__('resources.property_assignment.tab_submitted'))
                ->badge($this->orderByAssignmentStatus('submitted') ?? 0)
                ->badgeColor(Color::Indigo),
            Tab::make(__('resources.property_assignment.tab_approved'))
                ->badge($this->orderByAssignmentStatus('approved') ?? 0)
                ->badgeColor(Color::Green),
            Tab::make(__('resources.property_assignment.tab_rejected'))
                ->badge($this->orderByAssignmentStatus('rejected') ?? 0)
                ->badgeColor(Color::Red),
            Tab::make(__('resources.property_assignment.tab_published'))
                ->badge($this->orderByAssignmentStatus('published') ?? 0)
                ->badgeColor(Color::Teal),
            Tab::make(__('resources.property_assignment.tab_assigned'))
                ->badge($this->orderByAssignmentStatus('assigned') ?? 0)
                ->badgeColor(Color::Purple),
            Tab::make(__('resources.property_assignment.tab_finished'))
                ->badge($this->orderByAssignmentStatus('finished') ?? 0)
                ->badgeColor(Color::Lime),
        ];
    }

    public function orderByAssignmentStatus(?string $status = null){
        if(blank($status)){
            return PropertyAssignment::count();
        }
        return PropertyAssignment::where('property_assignment_status', $status)->count();
    }
}
