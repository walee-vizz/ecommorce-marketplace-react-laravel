<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\ProductVariationTypeEnum;
use Filament\Actions;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProductResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;

class ProductVariationTypes extends EditRecord
{
    protected static string $resource = ProductResource::class;
    protected static ?string $navigationIcon = 'heroicon-m-queue-list';
    protected static ?string $title = 'Product Variation Types';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('variationTypes')
                    ->label(false)
                    ->relationship()
                    ->collapsible()
                    ->defaultItems(1)
                    ->addActionLabel('Add new variation type')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        Select::make('type')
                            ->options(ProductVariationTypeEnum::labels())
                            ->required(),
                        Repeater::make('options')
                            ->relationship()
                            ->collapsible()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->columnSpan(2),
                                SpatieMediaLibraryFileUpload::make('images')
                                    ->image()
                                    ->multiple()
                                    ->openable()
                                    ->panelLayout('grid')
                                    ->collection('images')
                                    ->reorderable()
                                    ->appendFiles()
                                    ->preserveFilenames()
                                    ->columnSpan(3),
                            ])
                            ->columns(2)
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
