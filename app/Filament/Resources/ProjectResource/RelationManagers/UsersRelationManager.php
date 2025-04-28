<?php

namespace App\Filament\Resources\RoleResource\RelationManagers;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Assigned Users';

    protected static ?string $icon = 'heroicon-o-users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->hiddenOn('edit'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
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
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'active') {
                            $query->whereNull('deleted_at');
                        } elseif ($data['value'] === 'inactive') {
                            $query->whereNotNull('deleted_at');
                        }
                    }),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),

                        Forms\Components\Select::make('roles')
                            ->label('Additional Roles')
                            ->options(\App\Models\Role::where('id', '!=', $this->ownerRecord->id)->pluck('name', 'id'))
                            ->multiple(),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->before(function (User $record) {
                        if ($this->ownerRecord->name === 'admin' &&
                            $record->hasRole('admin') &&
                            User::role('admin')->count() <= 1) {
                            throw new \Exception('Cannot remove the last admin user');
                        }
                    }),

                Tables\Actions\EditAction::make()
                    ->url(fn (User $record): string => UserResource::getUrl('edit', ['record' => $record]))
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make()
                    ->before(function ($records) {
                        if ($this->ownerRecord->name === 'admin') {
                            $adminUsers = User::role('admin')->count();
                            $remainingAdmins = $adminUsers - $records->filter(fn ($user) => $user->hasRole('admin'))->count();

                            if ($remainingAdmins < 1) {
                                throw new \Exception('Cannot remove all admin users');
                            }
                        }
                    }),
            ])
            ->emptyStateActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                // $query->withTrashed();
            });
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Assigned Users (' . $ownerRecord->name . ')';
    }
}
