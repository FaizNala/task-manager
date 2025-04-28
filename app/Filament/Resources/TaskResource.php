<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Project;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->visible(fn () => auth()->user()->can('update_task_details')),

                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->visible(fn () => auth()->user()->can('update_task_details')),

                Forms\Components\Select::make('status')
                    ->options([
                        'to_do' => 'To Do',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                    ])
                    ->required(),

                Forms\Components\DateTimePicker::make('deadline')
                    ->required()
                    ->visible(fn () => auth()->user()->can('update_task_details')),

                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name', function ($query) {
                        if (!auth()->user()->can('view_any_project')) {
                            return $query->where('manager_id', auth()->id());
                        }
                        return $query;
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn () => auth()->user()->can('update_task_details')),

                Forms\Components\Select::make('assigned_to')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn () => auth()->user()->can('update_task_details')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('project.name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'to_do' => 'gray',
                        'in_progress' => 'warning',
                        'done' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()),

                Tables\Columns\TextColumn::make('deadline')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->label('Project')
                    ->options(function () {
                        // Jika user adalah manager, tampilkan hanya project yang dikelola
                        if (auth()->user()->hasRole('manager')) {
                            return Project::where('manager_id', auth()->id())->pluck('name', 'id')->toArray();
                        }
                        // Jika admin, tampilkan semua project
                        return Project::pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Assigned User'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'to_do' => 'To Do',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                    ]),

                Tables\Filters\Filter::make('deadline')
                    ->form([
                        Forms\Components\DatePicker::make('deadline_from'),
                        Forms\Components\DatePicker::make('deadline_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['deadline_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('deadline', '>=', $date),
                            )
                            ->when(
                                $data['deadline_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('deadline', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Task $record) => auth()->user()->can('delete_task')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->can('delete_task')),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                // Filter data untuk manager: hanya tampilkan task dalam project yang dikelola
                if (auth()->user()->hasRole('manager')) {
                    $query->whereHas('project', function ($q) {
                        $q->where('manager_id', auth()->id());
                    });
                }
                // Filter data untuk staff: hanya tampilkan task yang ditugaskan padanya
                elseif (auth()->user()->hasRole('staff')) {
                    $query->where('assigned_to', auth()->id());
                }
                // Admin dapat melihat semua task
            });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        // Manager: hitung task dari project yang dikelola
        if (auth()->user()->hasRole('manager')) {
            return static::getModel()::whereHas('project', function ($query) {
                $query->where('manager_id', auth()->id());
            })->count();
        }
        // Staff: hitung task yang ditugaskan padanya
        elseif (auth()->user()->hasRole('staff')) {
            return static::getModel()::where('assigned_to', auth()->id())->count();
        }
        // Admin: hitung semua task
        return static::getModel()::count();
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_task');
    }

    public static function canEdit(Model $record): bool
    {
        return
            auth()->user()->can('update_task') ||
            auth()->user()->can('update_task_details');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('delete_task');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_task');
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->can('view_task');
    }

    public static function afterSave(Task $task): void
    {
        if ($task->project) {
            $task->project->touch(); // Trigger project update
            $task->project->updateStatusBasedOnTasks();
        }
    }

    public static function afterDelete(Task $task): void
    {
        if ($task->project) {
            $task->project->touch(); // Trigger project update
            $task->project->updateStatusBasedOnTasks();
        }
    }
}
