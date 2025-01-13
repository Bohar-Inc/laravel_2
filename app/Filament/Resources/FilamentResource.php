<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FilamentResource\Pages;
use App\Filament\Resources\FilamentResource\RelationManagers;
use App\Models\Filament;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FilamentResource extends Resource
{
    protected static ?string $model = Filament::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make()->schema([
                    TextInput::make('name')
                        ->label('Route Name')
                        ->maxLength(50)
                        ->required(),
                ])->columnSpan(1),

                Forms\Components\Card::make()->schema([
                Map::make('route_coordinates')
                    ->label('Location')
                    ->columnSpanFull()
                    ->defaultLocation(latitude: 40.4168, longitude: -3.7038)
                    ->extraStyles([
                        'min-height: 80vh',
                        'border-radius: 10px'
                    ])
                    ->liveLocation(true, true, 5000)
                    ->showMarker()
                    ->markerColor("#22c55eff")
                    ->markerIconSize([32, 32])
                    ->markerIconClassName('my-marker-class')
                    ->markerIconAnchor([16, 32])
                    ->showFullscreenControl()
                    ->showZoomControl()
                    ->draggable()
                    ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                    ->zoom(15)
                    ->detectRetina()
                    ->showMyLocationButton()
                    ->geoMan(true)
                    ->geoManEditable(true)
                    ->geoManPosition('topleft')
                    ->drawCircleMarker()
                    ->rotateMode()
                    ->clickable(true) //click to move marker
                    ->drawPolyline()
                    ->dragMode()
                    ->deleteLayer()
                    ->setColor('#3388ff')
                    ->setFilledColor('#cad9ec')
                ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Route Name')
                ->searchable(),

                TextColumn::make('created_at')
                ->sortable()
                ->searchable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ViewAction::make()
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFilaments::route('/'),
            'create' => Pages\CreateFilament::route('/create'),
            'edit' => Pages\EditFilament::route('/{record}/edit'),
        ];
    }
}
