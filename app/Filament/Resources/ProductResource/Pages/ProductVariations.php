<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\ProductVariationTypeEnum;
use Filament\Actions;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProductResource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

class ProductVariations extends EditRecord
{
    protected static string $resource = ProductResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $title = 'Product Variations';

    public function form(Form $form): Form
    {
        $types = $this->record?->variationTypes;
        $fields = [];
        foreach ($types as $type) {
            $fields[] =
                TextInput::make("variation_type_" . $type->id . '.id')
                ->hidden();
            $fields[] =    TextInput::make("variation_type_" . $type->id . '.name')
                ->label($type->name);
        }
        // dd($fields);
        return $form
            ->schema([
                Repeater::make('variations')
                    ->addable(false)
                    ->collapsible()
                    ->label(false)
                    ->schema([
                        Section::make()
                            ->schema($fields)
                            ->columns(3),
                        TextInput::make('quantity')
                            ->numeric(),
                        TextInput::make('price')
                            ->numeric(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
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
        // dd($data);
        return $data;
    }

    private function mergeCartesianWithExisting($variationTypes, $existingData): array
    {
        $mergedVariations = [];
        $defaultQuantity = $this->record?->quantity;
        $defaultPrice = $this->record?->price;
        $cartesianProduct =  $this->cartesianProduct($variationTypes, $defaultQuantity, $defaultPrice);
        $mergedResults = [];
        foreach ($cartesianProduct as $product) {

            $optionIds = collect($product)
                ->filter(fn($value, $key) => str_starts_with($key, 'variation_type'))
                ->map(fn($option) => $option['id'])
                ->values()
                ->toArray();

            //Find matching entry in existing data
            $match = array_filter($existingData, function ($existingOption) use ($optionIds) {

                return $existingOption['variation_type_option_ids'] === $optionIds;
            });
            // dd($cartesianProduct, $existingData, $optionIds, $match);

            if (!empty($match)) {
                $existingEntry = reset($match);
                $product['id'] = $existingEntry['id'];
                $product['quantity'] = $existingEntry['quantity'];
                $product['price'] = $existingEntry['price'];
            } else {
                $product['quantity'] = $defaultQuantity;
                $product['price'] = $defaultPrice;
            }
            $mergedResults[] = $product;
        }
        return $mergedResults;
    }


    private function cartesianProduct($variationTypes, $quantity, $price)
    {
        $result = [[]];

        foreach ($variationTypes as $variationType) {
            $append = [];

            foreach ($variationType->options as $option) {

                foreach ($result as $combination) {
                    $newCombination = $combination + [
                        'variation_type_' . $variationType->id => [
                            'id' => $option->id,
                            'name' => $option->name,
                            'type' => $variationType->name,
                        ],
                    ];
                    $append[] = $newCombination;
                }
            }

            $result = $append;
        }

        foreach ($result as &$combination) {
            if (count($combination) == count($variationTypes)) {

                $item['quantity'] = $quantity;
                $item['price'] = $price;
            }
        }
        return $result;
    }



    protected function mutateFormDataBeforeSave(array $data): array
    {
        // dd($data);
        $formattedData = array();

        foreach ($data['variations'] as $variation) {
            $variationTypeOptionIds = [];

            foreach ($this->record->variationTypes as $variationType) {
                $variationTypeOptionIds[] = $variation['variation_type_' . $variationType->id]['id'];
            }
            $formattedData[] = [
                'id' => $variation['id'] ?? null,
                'product_id' => $this->record->id,
                'quantity' => $variation['quantity'],
                'price' => $variation['price'],
                'variation_type_option_ids' => $variationTypeOptionIds,
            ];
        }
        $data['variations'] = $formattedData;
        return $data;
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // dd($data);

        $variations = $data['variations'];
        // dd($variations);
        // $record->variations()->delete();
        $variations = collect($variations)->map(function ($variation) {
            return [
                'id' => $variation['id'] ?? null,
                'product_id' => $variation['product_id'],
                'variation_type_option_ids' => json_encode($variation['variation_type_option_ids']),
                'quantity' => $variation['quantity'],
                'price' => $variation['price'],
            ];
        })->toArray();

        $record->variations()->upsert($variations, ['id'], ['variation_type_option_ids', 'quantity', 'price']);
        unset($data['variations']);
        return $record;
    }
}
