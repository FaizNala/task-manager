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
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'to_do' => 'To Do',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('deadline')
                    ->required(),
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->hidden(fn () => auth()->user()->hasRole('staff')),
                Forms\Components\Select::make('assigned_to')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->hidden(fn () => auth()->user()->hasRole('staff')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->numeric()
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
                Tables\Filters\SelectFilter::make('id')
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
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Assigned User')
                    ->hidden(fn () => auth()->user()->hasRole('staff')),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('manager')) {
                    $query->whereHas('project', function ($q) {
                        $q->where('manager_id', auth()->id());
                    });
                } elseif (auth()->user()->hasRole('staff')) {
                    $query->where('assigned_to', auth()->id());
                }
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

    protected static function afterSave(Task $task)
    {
        $task->project->touch(); // Memicu update project
        $task->project->updateStatusBasedOnTasks();
    }

    protected static function afterDelete(Task $task)
    {
        $task->project->touch(); // Memicu update project
        $task->project->updateStatusBasedOnTasks();
    }
}
