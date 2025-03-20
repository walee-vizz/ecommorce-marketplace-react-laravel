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

class ProductVariations extends EditRecord
{
    protected static string $resource = ProductResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $title = 'Product Variations';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        $variations = $this->record?->variations?->toArray();
        $data['variations'] = $this->mergeCartesianWithExisting($this->record?->variationTypes, $variations);

        return $data;
    }

    public function mergeCartesianWithExisting($variationTypes, $variations): array
    {
        $mergedVariations = [];
        $defaultQuantity = $this->record?->quantity;
        $defaultPrice = $this->record?->price;
        $cartesianProduct =  $this->cartesianProduct($variationTypes, $defaultQuantity, $defaultPrice);
        $mergedResults = [];

        // foreach ($cartesianProduct as $product) {

        //     $optionIds = collect($product)
        //     ->filter(fn($value, $key) => str_starts_with($key, 'variation_type'))
        //     ->map(fn($option) => $option['id'])
        //     ->values()
        //     ->toArray();

        //     //Find matching entry in existing data
        //     $match = array_filter( $existingData, function($existingOption) use ($optionIds){

        //         return $existingOption['variation_type_option_ids'] === $optionIds;
        //     });

        // }
        return $mergedVariations;
    }


    public function cartesianProduct($types, $quantity, $price) {}
}

                      // Data Sample
// [
//     {
//         "variation_type_1":{
//             "id":1,
//             "name":"Black",
//             "type":"Color"
//         },
//         "variation_type_2":{
//             "id":4,
//             "name":"Small",
//             "type":"Size"
//         },
//         "quantity":10,
//         "price":13.00,
//     },
//     {
//         "variation_type_1":{
//             "id":1,
//             "name":"Black",
//             "type":"Color"
//         },
//         "variation_type_2":{
//             "id":5,
//             "name":"Medium",
//             "type":"Size"
//         },
//         "quantity":10,
//         "price":13.00,
//     }
//     {
//         "variation_type_1":{
//             "id":2,
//             "name":"White",
//             "type":"Color"
//         },
//         "variation_type_2":{
//             "id":4,
//             "name":"Small",
//             "type":"Size"
//         },
//         "quantity":10,
//         "price":13.00,
//     }
//     {
//         "variation_type_1":{
//             "id":2,
//             "name":"White",
//             "type":"Color"
//         },
//         "variation_type_2":{
//             "id":5,
//             "name":"Medium",
//             "type":"Size"
//         },
//         "quantity":10,
//         "price":13.00,
//     }
//     {
//         "variation_type_1":{
//             "id":3,
//             "name":"Green",
//             "type":"Color"
//         },
//         "variation_type_2":{
//             "id":4,
//             "name":"Small",
//             "type":"Size"
//         },
//         "quantity":10,
//         "price":13.00,
//     },
//     {
//         "variation_type_1":{
//             "id":3,
//             "name":"Green",
//             "type":"Color"
//         },
//         "variation_type_2":{
//             "id":5,
//             "name":"Medium",
//             "type":"Size"
//         },
//         "quantity":10,
//         "price":13.00,
//     }
// ]
