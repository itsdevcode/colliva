<?php

namespace App\Filament\Resources\SupportArticles;

use App\Filament\Resources\SupportArticles\Pages\ManageSupportArticles;
use App\Models\SupportArticle;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SupportArticleResource extends Resource
{
    protected static ?string $model = SupportArticle::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('support_category_id')->relationship('category', 'name')->required()->searchable()->preload(), TextInput::make('title')->required()->maxLength(255), TextInput::make('slug')->required()->maxLength(255)->regex('/^[a-z0-9-]+$/')->unique(ignoreRecord: true), Textarea::make('summary')->required()->maxLength(1000)->columnSpanFull(), Textarea::make('content')->required()->rows(16)->maxLength(50000)->helperText('Plain text. Use blank lines between paragraphs. HTML is displayed as text.')->columnSpanFull(), Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->default('draft')->required(), DateTimePicker::make('published_at')->helperText('Published articles appear only after this date.')->requiredIf('status', 'published'), TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable()->sortable()->wrap(), TextColumn::make('category.name'), TextColumn::make('status')->badge(), TextColumn::make('published_at')->dateTime()->sortable()])->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published']), SelectFilter::make('category')->relationship('category', 'name')])->recordActions([EditAction::make()])->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return ['index' => ManageSupportArticles::route('/')];
    }
}
