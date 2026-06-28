<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlexterListResource\Pages;
use App\Filament\Resources\FlexterListResource\RelationManagers\ItemsRelationManager;
use App\Genre\Models\Genre;
use App\List\Models\FlexterList;
use App\List\Support\ListIcons;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FlexterListResource extends Resource
{
    protected static ?string $model = FlexterList::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Curated Lists';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->whereNull('user_id');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('List details')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('icon')
                        ->label('List icon')
                        ->options(fn (): array => ListIcons::options())
                        ->default(ListIcons::DEFAULT)
                        ->required()
                        ->searchable()
                        ->helperText('Shown on the home page and list browse pages — e.g. 👻 for horror.'),
                    Forms\Components\Toggle::make('is_featured'),
                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(2),

            Forms\Components\Section::make('Auto-generation rules')
                ->description('The catalogue is queried when the list is created or regenerated. Titles are picked by popularity among matches.')
                ->schema([
                    Forms\Components\Select::make('genre_ids')
                        ->label('Genres')
                        ->multiple()
                        ->required()
                        ->options(fn (): array => Genre::query()->orderBy('genre')->pluck('genre', 'id')->all())
                        ->searchable()
                        ->helperText('Required. Titles must match at least one selected genre.'),
                    Forms\Components\Select::make('media_type')
                        ->label('Catalogue')
                        ->options([
                            'movie' => 'Movies only',
                            'tv' => 'Series only',
                            'both' => 'Movies & series',
                        ])
                        ->default('movie')
                        ->required(),
                    Forms\Components\TextInput::make('item_limit')
                        ->label('Number of titles')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(100)
                        ->default(20)
                        ->required(),
                    Forms\Components\TextInput::make('min_rating')
                        ->label('Minimum rating')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(10)
                        ->step(0.1)
                        ->placeholder('Any')
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? (float) $state : null)
                        ->helperText('Optional. TMDB score must be at or above this value.'),
                    Forms\Components\TextInput::make('min_year')
                        ->label('Minimum year')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue((int) date('Y'))
                        ->placeholder('Any')
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? (int) $state : null)
                        ->helperText('Optional. Release year must be this year or later.'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('icon')
                    ->label('Icon')
                    ->formatStateUsing(fn (?string $state): string => ListIcons::options()[ListIcons::normalize($state)] ?? $state),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('item_limit')->label('Limit'),
                Tables\Columns\TextColumn::make('min_rating')->label('Min rating')->placeholder('—'),
                Tables\Columns\TextColumn::make('min_year')->label('Min year')->placeholder('—'),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Items'),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlexterLists::route('/'),
            'create' => Pages\CreateFlexterList::route('/create'),
            'edit' => Pages\EditFlexterList::route('/{record}/edit'),
        ];
    }
}
