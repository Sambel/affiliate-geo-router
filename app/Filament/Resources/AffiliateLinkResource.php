<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AffiliateLinkResource\Pages;
use App\Filament\Resources\AffiliateLinkResource\RelationManagers;
use App\Models\AffiliateLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AffiliateLinkResource extends Resource
{
    protected static ?string $model = AffiliateLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('operator_id')
                    ->relationship('operator', 'name')
                    ->required(),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required(),
                Forms\Components\Textarea::make('url')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('priority')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('operator.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('affiliate_url')
                    ->label('Lien Affilié')
                    ->getStateUsing(function (AffiliateLink $record): string {
                        return config('app.url') . '/' . $record->operator->slug;
                    })
                    ->copyable()
                    ->copyableState(function (AffiliateLink $record): string {
                        return config('app.url') . '/' . $record->operator->slug;
                    })
                    ->tooltip('Cliquez pour copier le lien')
                    ->wrap(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('priority')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListAffiliateLinks::route('/'),
            'create' => Pages\CreateAffiliateLink::route('/create'),
            'edit' => Pages\EditAffiliateLink::route('/{record}/edit'),
        ];
    }
}
