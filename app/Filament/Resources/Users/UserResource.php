<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\AuditLog;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(255), TextInput::make('email')->email()->required()->maxLength(255)->unique(ignoreRecord: true)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable()->sortable(), TextColumn::make('email')->searchable()->sortable(), TextColumn::make('role')->sortable(), TextColumn::make('email_verified_at')->sortable()])->recordActions([ViewAction::make(), EditAction::make()->mutateDataUsing(function (array $data, User $record): array {
            if ($data['email'] !== $record->email) {
                $record->forceFill(['email_verified_at' => null])->save();
            }

            return $data;
        }), Action::make('changeRole')->schema([Select::make('role')->options(['creator' => 'Creator', 'brand' => 'Brand', 'admin' => 'Admin'])->required()])->requiresConfirmation()->action(function (User $record, array $data): void {
            abort_unless(auth()->user()->role === 'admin' && $record->id !== auth()->id(), 403);
            $record->forceFill(['role' => $data['role']])->save();
            AuditLog::create(['user_id' => auth()->id(), 'action' => 'user.role_changed', 'subject_type' => User::class, 'subject_id' => $record->id, 'note' => $data['role']]);
        })])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
