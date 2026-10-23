<?php

namespace App\Filament\Resources\Campaigns;

use App\Filament\Resources\Campaigns\Pages\ManageCampaigns;
use App\Models\AuditLog;
use App\Models\Campaign;
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
use Illuminate\Support\Facades\DB;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

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
        return $schema->components([TextInput::make('title'), Textarea::make('description'), Textarea::make('deliverables'), Textarea::make('requirements'), TextInput::make('budget_minor'), TextInput::make('status')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable()->sortable(), TextColumn::make('brandProfile.company_name')->sortable(), TextColumn::make('status')->sortable(), TextColumn::make('budget_minor')->sortable(), TextColumn::make('application_deadline')->sortable()])->recordActions([ViewAction::make(), Action::make('approve')->visible(fn (Campaign $record): bool => $record->status === 'pending')->action(function (Campaign $record): void {
            abort_unless(auth()->user()->role === 'admin', 403);
            DB::transaction(function () use ($record) {
                $record = Campaign::lockForUpdate()->findOrFail($record->id);
                abort_unless($record->status === 'pending', 422);
                $record->forceFill(['status' => 'published'])->save();
                AuditLog::create(['user_id' => auth()->id(), 'action' => 'campaign.published', 'subject_type' => Campaign::class, 'subject_id' => $record->id]);
            });
        }), Action::make('reject')->visible(fn (Campaign $record): bool => $record->status === 'pending')->action(function (Campaign $record): void {
            abort_unless(auth()->user()->role === 'admin', 403);
            DB::transaction(function () use ($record) {
                $record = Campaign::lockForUpdate()->findOrFail($record->id);
                abort_unless($record->status === 'pending', 422);
                $record->forceFill(['status' => 'cancelled'])->save();
                AuditLog::create(['user_id' => auth()->id(), 'action' => 'campaign.cancelled', 'subject_type' => Campaign::class, 'subject_id' => $record->id]);
            });
        })])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCampaigns::route('/'),
        ];
    }
}
