<?php

namespace App\Filament\Resources\ContentReports;

use App\Filament\Resources\ContentReports\Pages\ManageContentReports;
use App\Models\ContentReport;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContentReportResource extends Resource
{
    protected static ?string $model = ContentReport::class;

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
        return $schema->components([Textarea::make('reason'), TextInput::make('status')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('creatorProfile.display_name')->sortable(), TextColumn::make('reason')->sortable(), TextColumn::make('status')->sortable()])->recordActions([ViewAction::make(), Action::make('resolve')->action(function (ContentReport $record): void {
            abort_unless(auth()->user()->role === 'admin', 403);
            $record->forceFill(['status' => 'resolved'])->save();
        })])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContentReports::route('/'),
        ];
    }
}
