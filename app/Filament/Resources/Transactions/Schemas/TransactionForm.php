<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Item;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('user_id')
                //     ->required()
                //     ->numeric(),
                Hidden::make('user_id')->default(auth()->id()),
                Hidden::make('date')
                    ->default(now())
                    ->required(),

                Section::make('Payment')
                    ->schema([
                        TextInput::make('pay_total')
                            ->required()
                            ->prefix('Rp')
                            ->numeric(),
                         TextInput::make('change')
                            ->required()
                            ->prefix('Rp')
                            ->numeric(),
                    ]),

                Section::make('Cart')
                    ->schema([
                    Repeater::make('details')
                        ->relationship('details')
                        ->schema([
                            Select::make('item_id')
                                ->relationship('item', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->label('Product')
                                ->reactive()
                                ->afterStateUpdated(function ($state,  $set) {
                                        $item = Item::find($state);
                                        
                                        $set('price', $item ? $item->price : 0);
                                        
                                        $set('subtotal', $item ? $item->price : 0);
                                        $set('qty', 1); 
                                    }),
                            TextInput::make('qty')
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                ->required()
                                ->reactive() 
                                ->afterStateUpdated(fn ($state,  $get, $set) => 
                                    $set('subtotal', $state * $get('price')) 
                                ),

                            TextInput::make('subtotal')
                                ->numeric()
                                ->readOnly()
                                ->prefix('Rp')
                                ->dehydrated(),
                        ])->columns(3),
                        
                        TextInput::make('total')
                            ->required()
                            ->prefix('Rp')
                            ->numeric(),  
                    ]),

              
            ]);
    }
}
