<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Vendor;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\VendorResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VendorResource\RelationManagers;
use Filament\Tables\Columns\TextColumn;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_vendor');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_vendor');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_vendor');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_vendor');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->dehydrated(false),
                TextInput::make('store_name')
                    ->required(),
                TextInput::make('store_address'),
                TextInput::make('status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('store_name'),
                TextColumn::make('store_address'),
                // TextColumn::make('user.name'),
                TextColumn::make('status'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
