<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                ->label('Order ID'),

                TextColumn::make('grand_total')
                ->money('LKR'),

                TextColumn::make('status')
                ->badge()
                ->color(fn(string $state):string => match ($state) {
                    'new'=>'info',
                    'processing'=>'warning',
                    'shipped'=>'info',
                    'delivered'=>'success',
                    'cancelled'=>'danger',
                })
                ->icon(fn(string $state):string => match ($state){
                    'new'=>'heroicon-o-sparkles',
                    'processing'=>'heroicon-o-arrow-path',
                    'shipped'=>'heroicon-o-truck',
                    'delivered'=>'heroicon-o-check-badge',
                    'cancelled'=>'heroicon-o-x-circle',
                })
                ->sortable(),

                TextColumn::make('payment_method')
                ->sortable()
                ->searchable(),

                TextColumn::make('payment_status')
                ->sortable()
                ->searchable(),

                TextColumn::make('created_at')
                ->label('Order Date')
                ->dateTime()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('View Order')->url(fn(Order $record): string => OrderResource::getUrl('view',['record'=> $record]))
                ->color('info')
                ->icon('heroicon-o-eye'),
                DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
