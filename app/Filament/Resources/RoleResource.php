<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Support\Utils;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament-shield::filament-shield.field.name'))
                            ->disabled(),

                        Forms\Components\TextInput::make('guard_name')
                            ->label(__('filament-shield::filament-shield.field.guard_name'))
                            ->disabled(),

                        Forms\Components\CheckboxList::make('permissions')
                            ->label(__('filament-shield::filament-shield.field.permissions'))
                            ->relationship('permissions', 'name')
                            ->disabled()
                            ->gridDirection('row')
                            ->columns([
                                'default' => 2,
                                'lg' => 3,
                            ])
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->formatStateUsing(fn ($state): string => str($state)->headline())
                    ->colors(['primary'])
                    ->searchable(),

                Tables\Columns\TextColumn::make('guard_name')
                    ->label(__('filament-shield::filament-shield.column.guard_name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guard_name')
                    ->label('Guard Name')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                if (!auth()->user()->hasRole('admin')) {
                    $query->where('name', '!=', 'admin');
                }
            });
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
            'index' => Pages\ListRoles::route('/'),
            'view' => Pages\ViewRole::route('/{record}'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.roles');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
