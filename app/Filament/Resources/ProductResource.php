<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Details')
                ->schema([

                        TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                        TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),

                        FileUpload::make('thumbnail')
                        ->image()
                        ->required(),

                        Repeater::make('photos')
                        ->relationship('photos')
                        ->schema( [
                                FileUpload::make('photo')
                                ->required()
                            ] ),

                        Repeater::make('sizes')
                        ->relationship('sizes')
                        ->schema([
                            TextInput::make('size')
                            ->required()
                        ])

                    ]),

                    Fieldset::make('Additional')
                    ->schema([

                        Textarea::make('about')
                        ->required(),

                        Select::make('is_popular')
                        ->required()
                        ->options([
                            true => 'Popular',
                            false => 'Not Popular'
                        ]),

                        Select::make('category_id')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                        Select::make('brand_id')
                        ->relationship('brand', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                        TextInput::make('stock')
                        ->required()
                        ->numeric()
                        ->prefix('Qty'),


                    ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                ->searchable(),

                TextColumn::make('category.name'),

                TextColumn::make('brand.name'),

                TextColumn::make('price')
                ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.'))
                ->prefix('Rp'),


                TextColumn::make('stock'),

                ImageColumn::make('thumbnail'),

                IconColumn::make('is_popular')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Popular')

            ])
            ->filters([

                SelectFilter::make('category_id')
                ->label('category')
                ->relationship('category','name'),

                SelectFilter::make('brand_id')
                ->label('brand')
                ->relationship('brand','name'),

            ])
            ->actions([
                ViewAction::make('view')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
