<?php

namespace App\Filament\Pages;

use App\Models\TmdbApiActivity;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TmdbApiActivityPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-signal';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'TMDB API Activity';

    protected static ?string $title = 'TMDB API Activity';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.tmdb-api-activity';

    public ?string $date = null;

    /** @var array<string, mixed> */
    public array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->form->fill(['date' => $this->date]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->label('Day')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->live()
                    ->afterStateUpdated(function (?string $state): void {
                        $this->date = $state ?? now()->toDateString();
                        $this->resetTable();
                    }),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => TmdbApiActivity::query()
                ->whereDate('created_at', $this->date ?? now()->toDateString())
                ->latest('created_at'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('H:i:s')
                    ->sortable(),
                TextColumn::make('operation')
                    ->label('Operation')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()->toString()),
                TextColumn::make('media_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? str($state)->title()->toString() : '—'),
                TextColumn::make('category')
                    ->label('Category')
                    ->placeholder('—'),
                TextColumn::make('request_count')
                    ->label('Requests')
                    ->alignCenter(),
                TextColumn::make('items_fetched')
                    ->label('Items Fetched')
                    ->alignCenter(),
                TextColumn::make('source')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str($state)->upper()->toString()),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'success' ? 'success' : 'danger'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    /**
     * @return array<string, int>
     */
    public function getDayStatsProperty(): array
    {
        $activities = TmdbApiActivity::query()
            ->whereDate('created_at', $this->date ?? now()->toDateString())
            ->get();

        return [
            'movies' => (int) $activities->where('media_type', 'movie')->sum('items_fetched'),
            'tv' => (int) $activities->where('media_type', 'tv')->sum('items_fetched'),
            'actors' => (int) $activities->where('media_type', 'person')->sum('items_fetched'),
            'requests' => (int) $activities->sum('request_count'),
            'activities' => $activities->count(),
        ];
    }
}
