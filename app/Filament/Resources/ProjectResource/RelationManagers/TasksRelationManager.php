<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
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
                Forms\Components\Select::make('assigned_to')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->hidden(fn () => auth()->user()->hasRole('staff')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->hidden(fn () => auth()->user()->hasRole('staff')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn (Task $record) =>
                        auth()->user()->hasRole('staff') && $record->assigned_to !== auth()->id()),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn () => auth()->user()->hasRole('staff')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn () => auth()->user()->hasRole('staff')),
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

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Project Tasks';
    }

    public static function canViewForRecord(object $ownerRecord, string $pageClass): bool
    {
        // Only show relation manager if user has permission to view tasks
        return auth()->user()->can('view_task');
    }
}
