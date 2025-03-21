<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms\Form;
use App\Models\Department;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\ProductStatusEnum;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ProductImages;
use App\Filament\Resources\ProductResource\Pages\ProductVariations;
use App\Filament\Resources\ProductResource\Pages\ProductVariationTypes;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_product');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_product');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_product');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_product');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->forVendor();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->live('', "500ms")
                    ->required()
                    ->minLength(5)
                    ->maxLength(150)
                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                        if ($operation == 'string') {
                            return;
                        }
                        $set('slug', Str::slug($state));
                    })
                    ->columnSpan(2),
                TextInput::make('slug')
                    ->label(__('Slug'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpan(2),
                Forms\Components\Select::make('department_id')
                    ->label(__('Department'))
                    ->relationship('department', 'name')
                    ->options(Department::isActive()->get()->pluck('name', 'id'))
                    ->preload()
                    ->searchable()
                    ->reactive()
                    ->live()
                    ->afterStateUpdated(function (callable $set) {
                        $set('category_id', null);
                    })
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->label(__('Category'))
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, callable $get) {
                            if ($get('department_id')) {
                                // dd($get('department_id'));
                                return $query->isActive()->where('department_id', $get('department_id'));
                            }
                        }
                    )
                    ->options(function (callable $get) {
                        $depId = $get('department_id');
                        if ($depId) {
                            return \App\Models\Category::isActive()->where('department_id', $depId)->pluck('name', 'id');
                        }
                        return [];
                    })
                    // ->reactive()
                    // ->live()
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('stock')
                    ->required()
                    ->numeric(),
                TextInput::make('cost')
                    ->required()
                    ->numeric(),
                TextInput::make('price')
                    ->required()
                    ->numeric(),
                RichEditor::make('description')
                    ->required()
                    ->fileAttachmentsDirectory('products/images')
                    ->toolbarButtons([
                        'blockquote',
                        'bold',
                        'italic',
                        'bulletList',
                        'h2',
                        'h3',
                        'underline',
                        'strike',
                        'link',
                        'image',
                        'table',
                        'orderedList',
                        'redo',
                        'undo',
                    ])
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label(__('Status'))
                    ->options(ProductStatusEnum::labels())
                    ->default(ProductStatusEnum::Draft->value)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')
                    ->collection('images')
                    ->limit(1)
                    ->conversion('thumb')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('slug')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('department.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('category.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('stock')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('cost')->sortable()->searchable()->formatStateUsing(function ($state) {
                    return number_format($state, 2, '.', ',');
                }),
                Tables\Columns\TextColumn::make('price')->sortable()->searchable()->formatStateUsing(function ($state) {
                    return number_format($state, 2, '.', ',');
                }),
                Tables\Columns\TextColumn::make('status')->badge()->colors(ProductStatusEnum::colors()),
                Tables\Columns\TextColumn::make('created_at')->date('d-M-Y'),
                Tables\Columns\TextColumn::make('updated_at')->date('d-M-Y'),
            ])
            ->filters([
                SelectFilter::make('status')->options(ProductStatusEnum::labels()),
                SelectFilter::make('department_id')->relationship('department', 'name')
                    ->options(Department::isActive()->get()->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'edit' => EditProduct::route('/{record}/edit'),
            'images' => ProductImages::route('{record}/images'),
            'variation-types' => ProductVariationTypes::route('{record}/variation-types'),
            'variations' => ProductVariations::route('{record}/variations'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditProduct::class,
            ProductImages::class,
            ProductVariationTypes::class,
            ProductVariations::class,
        ]);
    }
}
