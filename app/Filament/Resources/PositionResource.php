<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PositionResource\Pages;
use App\Filament\Resources\PositionResource\RelationManagers;
use App\Models\Position;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('department')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('department')
                    ->searchable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Total Employees'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->options(function () {
                        // Mengambil semua nilai unik dari kolom department
                        return Position::query()
                            ->distinct()
                            ->pluck('department', 'department')
                            ->toArray();
                    })
                    ->label('Department')
                    ->multiple()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Position $record) => auth()->user()->can('update_position')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Position $record) => auth()->user()->can('delete_position'))
                    ->before(function ($record) {
                        if ($record->users()->exists()) {
                            throw new \Exception('Cant delete this position because it has employees assigned to it.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->can('delete_position')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPositions::route('/'),
            'create' => Pages\CreatePosition::route('/create'),
            'edit' => Pages\EditPosition::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_position');
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->can('view_position');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_position');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('update_position');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('delete_position');
    }
}
