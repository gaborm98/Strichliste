<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;


class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'hugeicons-vegetarian-food';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->options([
                        'drink' => 'Getränk',
                        'food' => 'Snack',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set, $state) => $set('drink_type', $state === 'drink' ? 'non_alcoholic' : null)),
                Select::make('drink_type')
                    ->options([
                        'alcoholic' => 'Alkoholisch',
                        'non_alcoholic' => 'Nicht Alkoholisch',
                    ])
                    ->hidden(fn(callable $get) => $get('type') !== 'drink'),
                TextInput::make('price')
                    ->numeric()
                    ->required(),
                Textarea::make('description'),
                TextInput::make('stock')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->icon('heroicon-m-envelope')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        if ($state === 'drink') {
                            return 'Getränk';
                        } elseif ($state === 'food') {
                            return 'Essen';
                        }
                        return $state;
                    })
                    ->icon(function ($state) {
                        if ($state === 'drink') {
                            return 'carbon-drink-01';
                        } elseif ($state === 'food') {
                            return 'fluentui-food-apple-20-o';
                        }
                        return null;
                    })
                    ->color(function ($state) {
                        if ($state === 'drink') {
                            return 'info';
                        } elseif ($state === 'food') {
                            return 'warning';
                        }
                        return null;
                    }),
                TextColumn::make('drink_type')
                    ->badge()
                    ->label('Getränketyp')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        if ($state === 'alcoholic') {
                            return 'Alkoholisch';
                        } elseif ($state === 'non_alcoholic') {
                            return 'Nicht alkoholisch';
                        }
                        return $state;
                    })
                    ->icon(function ($state) {
                        if ($state === 'alcoholic') {
                            return 'lucide-beer';
                        } elseif ($state === 'non_alcoholic') {
                            return 'lucide-beer-off';
                        }
                        return null;
                    })
                    ->color(function ($state) {
                        if ($state === 'alcoholic') {
                            return 'danger';
                        } elseif ($state === 'non_alcoholic') {
                            return 'success';
                        }
                        return null;
                    }),
                TextColumn::make('price')
                    ->label('Preis')
                    ->sortable()
                    ->money('EUR'),
                TextColumn::make('stock')
                    ->label('Bestand')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //Tables\Actions\BulkActionGroup::make([
                //    Tables\Actions\DeleteBulkAction::make(),
                //]),
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
