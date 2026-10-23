<?php

namespace App\Filament\Resources\CreatorProfiles;

use App\Actions\ModerateCreator;
use App\Filament\Resources\CreatorProfiles\Pages\ManageCreatorProfiles;
use App\Models\AuditLog;
use App\Models\CreatorProfile;
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

class CreatorProfileResource extends Resource
{
    protected static ?string $model = CreatorProfile::class;

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
        return $schema->components([TextInput::make('display_name'), TextInput::make('slug'), TextInput::make('status'), Textarea::make('bio'), TextInput::make('photo_path'), TextInput::make('publication_consented_at')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('display_name')->searchable()->sortable(), TextColumn::make('location.name')->sortable(), TextColumn::make('category.name')->sortable(), TextColumn::make('status')->sortable(), TextColumn::make('verified_at')->sortable()])->recordActions([ViewAction::make(), Action::make('approve')->visible(fn (CreatorProfile $record): bool => $record->status === 'pending')->action(fn (CreatorProfile $record) => app(ModerateCreator::class)->handle(auth()->user(), $record, 'approved')), Action::make('reject')->schema([Textarea::make('note')->required()->maxLength(2000)])->visible(fn (CreatorProfile $record): bool => $record->status === 'pending')->action(fn (CreatorProfile $record, array $data) => app(ModerateCreator::class)->handle(auth()->user(), $record, 'rejected', $data['note'])), Action::make('verifyIdentity')->requiresConfirmation()->action(function (CreatorProfile $record): void {
            abort_unless(auth()->user()->role === 'admin', 403);
            $record->forceFill(['verified_at' => now()])->save();
            AuditLog::create(['user_id' => auth()->id(), 'action' => 'creator.identity_verified', 'subject_type' => CreatorProfile::class, 'subject_id' => $record->id]);
        }), Action::make('unpublish')->requiresConfirmation()->action(function (CreatorProfile $record): void {
            abort_unless(auth()->user()->role === 'admin', 403);
            $record->forceFill(['status' => 'rejected'])->save();
        })])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCreatorProfiles::route('/'),
        ];
    }
}
