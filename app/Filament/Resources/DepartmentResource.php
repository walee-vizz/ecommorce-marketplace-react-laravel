<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Department;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Filament\Resources\DepartmentResource\RelationManagers\CategoriesRelationManager;
use Filament\Tables\Columns\IconColumn;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_department');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_department');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_department');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_department');
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->live('', "1000ms")
                    ->required()
                    ->minLength(5)
                    ->maxLength(150)
                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                        if ($operation == 'string') {
                            return;
                        }
                        $set('slug', Str::slug($state));
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(150)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('meta_title')
                    ->maxLength(100),
                Forms\Components\TextInput::make('meta_description')
                    ->maxLength(255),
                Forms\Components\Toggle::make('active')
                    ->default(true),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('slug')->sortable()->searchable(),
                TextColumn::make('meta_title')->sortable()->searchable(),
                TextColumn::make('meta_description')->sortable()->searchable(),
                IconColumn::make('active')->boolean(),
                TextColumn::make('created_at')->date('d-M-Y')->sortable()->toggleable(),
                TextColumn::make('updated_at')->date('d-M-Y')->sortable()->toggleable(),
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
            CategoriesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
