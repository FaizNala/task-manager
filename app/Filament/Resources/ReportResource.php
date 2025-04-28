<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class ReportResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $navigationLabel = 'Task Reports';
    protected static ?string $modelLabel = 'Task Report';


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Project')
                    ->searchable()
                    ->hidden(fn() => auth()->user()->hasRole('staff')),
                TextColumn::make('manager.name')
                    ->label('Manager')
                    ->hidden(fn() => auth()->user()->hasRole('staff')),
                TextColumn::make('to_do_count')
                    ->label('To Do')
                    ->numeric(),
                TextColumn::make('in_progress_count')
                    ->label('In Progress')
                    ->numeric(),
                TextColumn::make('done_count')
                    ->label('Done')
                    ->numeric(),
                TextColumn::make('total_tasks')
                    ->label('Total Tasks')
                    ->numeric()
                    ->getStateUsing(fn($record) =>
                        $record->to_do_count + $record->in_progress_count + $record->done_count),
            ])
            ->actions([
                Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => route('filament.admin.resources.reports.export', [
                        'project_id' => $record->id,
                        'user_id' => request()->input('tableFilters.assigned_user.value'),
                        'start_date' => request()->input('tableFilters.date_range.start_date'),
                        'end_date' => request()->input('tableFilters.date_range.end_date'),
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->filters([
                SelectFilter::make('id')
                    ->label('Project')
                    ->options(function () {
                        if (auth()->user()->hasRole('manager')) {
                            return Project::where('manager_id', auth()->id())->pluck('name', 'id')->toArray();
                        }
                        return Project::pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->hidden(fn() => auth()->user()->hasRole('staff')),

                SelectFilter::make('manager_id')
                    ->label('Manager')
                    ->options(User::role('manager')->pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            $query->where('manager_id', $data['value']);
                        }
                        return $query;
                    })
                    ->hidden(fn() => auth()->user()->hasRole(['staff', 'manager'])),

                SelectFilter::make('assigned_user')
                    ->label('User')
                    ->options(User::query()->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            return $query->whereHas('tasks', function ($q) use ($data) {
                                $q->where('assigned_to', $data['value']);
                            });
                        }

                        return $query;
                    })
                    ->hidden(fn() => auth()->user()->hasRole('staff')),

                Filter::make('date_range')
                    ->form([
                        DatePicker::make('start_date'),
                        DatePicker::make('end_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn(Builder $query, $date): Builder => $query->whereHas(
                                    'tasks',
                                    fn($q) => $q->whereDate('created_at', '>=', $date)
                                ),
                            )
                            ->when(
                                $data['end_date'],
                                fn(Builder $query, $date): Builder => $query->whereHas(
                                    'tasks',
                                    fn($q) => $q->whereDate('created_at', '<=', $date)
                                ),
                            );
                    }),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->withCount([
                    'tasks as to_do_count' => fn($query) => $query->where('status', 'to_do'),
                    'tasks as in_progress_count' => fn($query) => $query->where('status', 'in_progress'),
                    'tasks as done_count' => fn($query) => $query->where('status', 'done'),
                ]);

                if (auth()->user()->hasRole('manager')) {
                    $query->where('manager_id', auth()->id());
                }
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['admin', 'manager']);
    }
}
