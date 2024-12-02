<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use App\Models\PromoCode;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\ProductTransaction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductTransactionResource\Pages;

class ProductTransactionResource extends Resource
{
    protected static ?string $model = ProductTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([

                    Step::make('Product and Price')
                    ->schema([

                        Grid::make(2)
                        ->schema([

                            Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated( function ($state, callable $get, callable $set) {

                                $product = Product::find($state);
                                $price = $product ? $product->price : 0;
                                $quantity = $get('quantity') ?? 1;
                                $subTotalAmount = $price * $quantity;  
                                
                                $set('price', $price);
                                $set('sub_total_amount', $subTotalAmount);
                                
                                $discount = $get('discount_amount') ?? 0;
                                $grandTotalAmount = $subTotalAmount - $discount;
                                $set('grand_total_amount', $grandTotalAmount);

                                $sizes = $product ? $product->sizes->pluck('size', 'id')->toArray() : [];
                                $set('product_sizes', $sizes);
                                
                            })
                            ->afterStateHydrated(function ( callable $get, callable $set, $state){
                                $productId = $state;
                                if($productId){
                                    $product = Product::find($productId);
                                    $sizes = $product ? $product->sizes->pluck('size', 'id')->toArray() : [];
                                    $set('product_sizes', $sizes);
                                }
                            }),

                            Select::make('product_size')
                            ->label('Product Size')
                            ->options(function (callable $get) {

                                $sizes = $get('product_sizes');
                                return is_array($sizes) ? $sizes : [];
    
                            })
                            ->required()
                            ->live(),

                            TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->prefix('Qty')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set ) {
                                $price = $get('price');
                                $quantity = $state;
                                $subTotalAmount = $price * $quantity;

                                $set('sub_total_amount', $subTotalAmount);

                                $discount = $get('discount_amount') ?? 0;
                                $grandTotalAmount = $subTotalAmount - $discount;
                                $set('grand_total_amount', $grandTotalAmount);
                            }),

                            Select::make('promo_code_id')
                            ->relationship('promoCode', 'code')
                            ->preload()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated( function ($state, callable $get, callable $set) {
                                $subTotalAmount = $get('sub_total_amount');
                                $promoCode = PromoCode::find($state);
                                $discount = $promoCode ? $promoCode->discount_amount : 0;

                                $set('discount_amount', $discount);

                                $grandTotalAmount = $subTotalAmount - $discount;
                                $set('grand_total_amount', $grandTotalAmount);
                            }),

                            TextInput::make('sub_total_amount')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->prefix('Rp'),

                            TextInput::make('grand_total_amount')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->prefix('Rp'),

                            TextInput::make('discount_amount')
                            // ->readOnly()
                            ->required()
                            ->numeric()
                            ->prefix('Rp')

                        ])
                    ]),

                    Step::make('Customer Information')
                    ->schema([

                        Grid::make(2)
                        ->Schema([

                            TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                            TextInput::make('phone')
                            ->required()
                            ->maxLength(255),

                            TextInput::make('email')
                            ->required()
                            ->maxLength(255),

                            TextInput::make('address')
                            ->required()
                            ->maxLength(255),

                            TextInput::make('city')
                            ->required()
                            ->maxLength(255),

                            TextInput::make('post_code')
                            ->required()
                            ->maxLength(255),

                        ])
                     ]),

                     Step::make('Payment Information')
                     ->schema([

                        TextInput::make('booking_trx_id')
                        ->required()
                        ->maxLength(255),
                        
                        FileUpload::make('proof')
                        ->image()
                        ->required(),

                        ToggleButtons::make('is_paid')
                        ->label('Apakah Sudah Membayar ?')
                        ->boolean()
                        ->grouped()
                        ->required()
                        ->icons([
                            true => 'heroicon-o-check-circle',
                            false => 'heroicon-o-clock'
                        ]),


                    ]),
                    
                ])
                ->columnSpan('full')
                ->columns(1)
                ->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable(),

                TextColumn::make('email')
                ->copyable()
                ->copyMessage('Email address copied')
                ->copyMessageDuration(1500),

                TextColumn::make('phone')
                ->copyable()
                ->copyMessage('Number phone copied')
                ->copyMessageDuration(1500),

                TextColumn::make('post_code')
                ->copyable()
                ->copyMessage('post code copied')
                ->copyMessageDuration(1500),

                TextColumn::make('booking_trx_id')
                ->searchable()
                ->weight('bold')
                ->copyable()
                ->label('Code Booking')
                ->copyMessage('Booking code copied')
                ->copyMessageDuration(1500),

                TextColumn::make('product.name')
                ->label('Product Name'),
                ImageColumn::make('product.thumbnail')
                ->label('Image'),
                TextColumn::make('quantity')
                ->label('Jumlah'),
                TextColumn::make('sub_total_amount')
                ->color('primary')
                ->prefix('Rp.'),
                TextColumn::make('discount_amount')
                ->color('danger')
                ->prefix('- Rp.'),
                TextColumn::make('grand_total_amount')
                ->color('success')
                ->prefix('Rp.'),
              
                IconColumn::make('is_paid')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Terverikasi'),

                
            ])
            ->filters([
                SelectFilter::make('product_id')
                ->label('product')
                ->relationship('product','name')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                ->label('Approve')
                ->action(function (ProductTransaction $record)  {
                    
                    $record->is_paid = true;
                    $record->save();

                    Notification::make()
                    ->title('Order Approved')
                    ->success()
                    ->body('the order has been successfully approved')
                    ->send();
                })
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (ProductTransaction $record) => !$record->is_paid),

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
            'index' => Pages\ListProductTransactions::route('/'),
            'create' => Pages\CreateProductTransaction::route('/create'),
            'edit' => Pages\EditProductTransaction::route('/{record}/edit'),
        ];
    }
}
