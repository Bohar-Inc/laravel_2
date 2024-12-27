<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int|string|array $columnSpan='full';

    protected static ?int $sort=2;

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at','desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Order ID'),

                TextColumn::make('user.name')
                ->searchable(),

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
            ->actions([
                Action::make('View Order')
                ->url(fn(Order $record): string => OrderResource::getUrl('view',['record'=>$record]))
                ->icon('heroicon-o-eye')
            ]);
    }
}
