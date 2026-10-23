<?php

namespace App\Filament\Resources\Collaborations;

use App\Filament\Resources\Collaborations\Pages\ManageCollaborations;
use App\Models\Collaboration;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CollaborationResource extends Resource
{
    protected static ?string $model = Collaboration::class;

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
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('collaboration_request_id'), TextInput::make('status'), TextInput::make('completed_at')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('collaboration_request_id')->sortable(), TextColumn::make('status')->sortable(), TextColumn::make('completed_at')->sortable()])->recordActions([ViewAction::make()])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCollaborations::route('/'),
        ];
    }
}
