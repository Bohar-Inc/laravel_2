<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use Filament\Infolists\Infolist;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 5;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                   Section::make('Order Information')->schema([
                        Select::make('user_id')
                       ->required()
                       ->relationship('user','name')
                       ->label('Customer'),

                       Select::make('payment_method')
                       ->options([
                           'stripe' => 'Stripe',
                           'cod'=>'Cash On Delivery',
                       ])
                       ->required(),

                       Select::make('payment_status')
                       ->options([
                           'pending' => 'Pending',
                           'paid' => 'Paid',
                           'failed' => 'Failed',
                       ])
                       ->default('pending')
                       ->required(),

                       ToggleButtons::make('status')
                       ->options([
                           'new' => 'New',
                           'processing' => 'Processing',
                           'shipped' => 'Shipped',
                           'delivered' => 'Delivered',
                           'cancelled' => 'Cancelled',
                       ])
                       ->inline()
                       ->icons([
                           'new'=>'heroicon-m-sparkles',
                           'processing'=>'heroicon-m-arrow-path',
                           'shipped'=>'heroicon-m-truck',
                           'delivered'=>'heroicon-m-check-badge',
                           'cancelled'=>'heroicon-m-x-circle',
                       ])
                       ->colors([
                           'new'=>'info',
                           'processing'=>'warning',
                           'shipped'=>'info',
                           'delivered'=>'success',
                           'cancelled'=>'danger',
                       ])
                       ->default('new'),

                       Select::make('currency')
                       ->options([
                           'inr' => 'INR',
                           'usd' => 'USD',
                           'eur' => 'EUR',
                           'lkr' => 'LKR',
                       ])
                       ->default('lkr'),

                       Select::make('shipping_method')
                       ->options([
                           'fedex' => 'Fedex',
                           'ups' => 'UPS',
                           'dhl' => 'DHL',
                           'usps' => 'USPS',
                       ]),

                       Textarea::make('notes')
                       ->columnSpanFull()
                    ])->columns(2),

                    Section::make('Order Items')->schema([
                        Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                            ->required()
                            ->relationship('product','name')
                            ->searchable()
                            ->preload()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->columnSpan(4)
                            ->reactive()
                            ->afterStateUpdated(fn($state,Set $set)=>$set('unit_amount',Product::find($state)->price ?? 0))
                            ->afterStateUpdated(fn($state,Set $set)=>$set('total_amount',Product::find($state)->price ?? 0)),

                            TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->columnSpan(2)
                            ->reactive()
                            ->afterStateUpdated(fn($state,Set $set,Get $get)=>$set('total_amount',$state*$get('unit_amount'))),

                            TextInput::make('unit_amount')
                            ->numeric()
                            ->required()
                            ->disabled()
                            ->columnSpan(3)
                            ->dehydrated(),

                            TextInput::make('total_amount')
                            ->numeric()
                            ->required()
                            ->disabled()
                            ->columnSpan(3)
                            ->dehydrated(),

                        ])->columns(12),
                        Placeholder::make('grand_total_placeholder')
                        ->label('Grand Total')
                        ->content(function (Get $get,Set  $set) {
                            $total=0;
                            if (!$repeaters = $get('items')) {
                                return $total;
                            }

                            foreach ($repeaters as $key => $repeater) {
                                $total += $get("items.{$key}.total_amount");
                            }
                            $set('grand_total',$total);
                            return Number::currency($total,'LKR');
                        }),
                        Hidden::make('grand_total')
                        ->default(0)
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                ->searchable()
                ->sortable()
                ->label('Customer'),

                TextColumn::make('grand_total')
                ->numeric()
                ->money('LKR')
                ->sortable(),

                TextColumn::make('payment_method')
                ->searchable()
                ->sortable(),

                TextColumn::make('payment_status')
                ->searchable()
                ->sortable(),

                TextColumn::make('currency')
            ->searchable()
            ->sortable(),

                TextColumn::make('shipping_method')
            ->searchable()
            ->sortable(),

                SelectColumn::make('status')
                ->options([
                    'new' => 'New',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ])
                ->searchable()
                ->sortable(),

                TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('Order Information')->schema([
                    Grid::make()->schema([
                        TextEntry::make('user.name')
                            ->label('Username'),
                        TextEntry::make('grand_total')
                            ->prefix('LKR'),
                        TextEntry::make('payment_method'),
                        TextEntry::make('payment_status'),
                        TextEntry::make('shipping_method'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn(string $state):string=> match ($state){
                                'new'=>'info',
                                'processing'=>'warning',
                                'shipped'=>'info',
                                'delivered'=>'success',
                                'cancelled'=>'danger',
                            })
                            ->icon(fn(string $state):string=> match ($state){
                                'new'=>'heroicon-m-sparkles',
                                'processing'=>'heroicon-m-arrow-path',
                                'shipped'=>'heroicon-m-truck',
                                'delivered'=>'heroicon-m-check-badge',
                                'cancelled'=>'heroicon-m-x-circle',
                            }),
                    ])->columns(3),
                ])->columnSpan(3),
                \Filament\Infolists\Components\Section::make('Product Image')->schema([
                    ImageEntry::make('')
                        ->getStateUsing(function ($record) {
                            // Retrieve the first order item.
                            $orderItem = $record->items->first();

                            // Check if the order item and its associated product exist.
                            if ($orderItem && $orderItem->product) {
                                // Get the images from the product.
                                $images = $orderItem->product->images;

                                // If the image attribute isn’t already an array, decode it.
                                if (!is_array($images)) {
                                    $images = json_decode($images, true);
                                }

                                // Return the first image (index 0) or null if it doesn’t exist.
                                return $images[0] ?? null;
                            }

                            return null;
                        })
                    ->alignCenter()
                    ->width('100%'),
                ])->columnSpan(1)

            ])->columns(4);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): array|string|null
    {
        return static::getModel()::count() > 10 ? 'danger' : 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
