<?php

namespace App\Filament\Resources\BrandProfiles;

use App\Filament\Resources\BrandProfiles\Pages\ManageBrandProfiles;
use App\Models\BrandProfile;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BrandProfileResource extends Resource
{
    protected static ?string $model = BrandProfile::class;

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
        return $schema->components([TextInput::make('company_name')->required()->maxLength(150), TextInput::make('website')->url()->maxLength(255), Textarea::make('description')->maxLength(3000)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('company_name')->searchable()->sortable(), TextColumn::make('user.email')->sortable(), TextColumn::make('location.name')->sortable()])->recordActions([ViewAction::make(), EditAction::make()])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBrandProfiles::route('/'),
        ];
    }
}
