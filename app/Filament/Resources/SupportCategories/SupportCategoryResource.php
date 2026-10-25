<?php

namespace App\Filament\Resources\SupportCategories;

use App\Filament\Resources\SupportCategories\Pages\ManageSupportCategories;
use App\Models\SupportCategory;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupportCategoryResource extends Resource
{
    protected static ?string $model = SupportCategory::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(100), TextInput::make('slug')->required()->maxLength(120)->regex('/^[a-z0-9-]+$/')->unique(ignoreRecord: true), Textarea::make('description')->maxLength(1000), TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable()->sortable(), TextColumn::make('slug')->searchable(), TextColumn::make('articles_count')->counts('articles')->label('Articles')])->filters([])->recordActions([EditAction::make()])->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return ['index' => ManageSupportCategories::route('/')];
    }
}
