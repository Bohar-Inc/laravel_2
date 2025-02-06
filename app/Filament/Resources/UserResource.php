<?php

namespace App\Filament\Resources;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'name';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->required()
                ->maxLength(255),

                TextInput::make('email')
                ->label('Email Address')
                ->required()
                ->maxLength(255)
                ->email()
                ->unique(ignoreRecord: true),

                DateTimePicker::make('email_verified_at')
                ->label('Email Verified At')
                ->default(now()),

                TextInput::make('password')
                ->password()
                ->required(fn(Page $livewire):bool =>$livewire instanceof CreateRecord)
                ->maxLength(10)
                ->dehydrated(fn($state)=>filled($state)),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->searchable(),
                TextColumn::make('name')
                ->searchable(),
                TextColumn::make('email')
                ->searchable(),
                TextColumn::make('email_verified_at')
                ->sortable()
                ->dateTime(),
                TextColumn::make('created_at')
                ->sortable()
                ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()->exporter(UserExporter::class)
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('Download PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(fn (Model $record) => static::exportPdf($record))
                        ->color('success'),
        ]),
                Action::make('Export All as PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        $users = User::all();
                        $pdf = Pdf::loadView('pdf.all_users', compact('users'));
                        return response()->streamDownload(fn () => print($pdf->output()), 'Users_List.pdf');
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function exportPdf(Model $record)
    {
        $pdf = PDF::loadView('pdf.user', ['user' => $record]); // Ensure 'pdf.user' exists
        return response()->streamDownload(fn () => print($pdf->output()), 'User_Details.pdf');
    }
    public static function getRelations(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
