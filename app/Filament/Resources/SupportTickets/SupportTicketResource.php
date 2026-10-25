<?php

namespace App\Filament\Resources\SupportTickets;

use App\Actions\ManageSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\ManageSupportTickets;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('reference'), TextEntry::make('status')->formatStateUsing(fn (string $state): string => SupportTicket::STATUSES[$state]),
            TextEntry::make('name'), TextEntry::make('email'), TextEntry::make('user_type'),
            TextEntry::make('category')->formatStateUsing(fn (string $state): string => SupportTicket::CATEGORIES[$state] ?? $state),
            TextEntry::make('subject')->columnSpanFull(), TextEntry::make('description')->columnSpanFull(), TextEntry::make('related_reference'),
            TextEntry::make('last_activity_at')->dateTime(), TextEntry::make('status_changed_at')->dateTime(),
            RepeatableEntry::make('messages')->label('Conversation and private staff notes')->columnSpanFull()->schema([
                TextEntry::make('visibility')->state(fn (SupportMessage $record): string => $record->internal ? 'INTERNAL NOTE · Staff only' : ($record->staff ? 'STAFF REPLY · Customer visible' : 'CUSTOMER FOLLOW-UP')),
                TextEntry::make('user.name')->placeholder('Former account'), TextEntry::make('created_at')->dateTime(), TextEntry::make('body')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('reference')->searchable(), TextColumn::make('subject')->searchable()->wrap(), TextColumn::make('email')->searchable(),
            TextColumn::make('user_type')->badge(), TextColumn::make('category')->formatStateUsing(fn (string $state): string => SupportTicket::CATEGORIES[$state] ?? $state),
            TextColumn::make('status')->formatStateUsing(fn (string $state): string => SupportTicket::STATUSES[$state])->badge(), TextColumn::make('last_activity_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(SupportTicket::STATUSES), SelectFilter::make('category')->options(SupportTicket::CATEGORIES),
        ])->recordActions([
            ViewAction::make()->beforeFormFilled(function (SupportTicket $record): void {
                $record->load(['messages' => fn ($query) => $query->orderBy('id')->with('user')]);
            }),
            Action::make('status')->label('Change status')->authorize('update')->schema([Select::make('status')->options(SupportTicket::STATUSES)->required()])
                ->fillForm(fn (SupportTicket $record): array => ['status' => $record->status])
                ->action(function (SupportTicket $record, array $data): void {
                    app(ManageSupportTicket::class)->status(auth()->user(), $record, $data['status']);
                }),
            Action::make('reply')->authorize('reply')->schema([Textarea::make('body')->label('Customer-visible reply')->required()->minLength(5)->maxLength(10000)->rows(7)])
                ->action(function (SupportTicket $record, array $data): void {
                    app(ManageSupportTicket::class)->respond(auth()->user(), $record, $data['body']);
                }),
            Action::make('note')->label('Internal note')->authorize('update')->schema([Textarea::make('body')->label('Private staff note (never visible to the customer)')->required()->minLength(5)->maxLength(10000)->rows(5)])
                ->action(function (SupportTicket $record, array $data): void {
                    app(ManageSupportTicket::class)->respond(auth()->user(), $record, $data['body'], true);
                }),
        ])->defaultSort('last_activity_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ManageSupportTickets::route('/')];
    }
}
