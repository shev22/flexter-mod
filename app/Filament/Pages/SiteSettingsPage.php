<?php

namespace App\Filament\Pages;

use App\Site\Data\SiteSettingsData;
use App\Shared\Support\HomeCache;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class SiteSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $title = 'Site Settings';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.site-settings';

    /** @var array<string, mixed> */
    public array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function mount(SiteSettingsServiceInterface $siteSettings): void
    {
        $this->form->fill($siteSettings->get()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')
                    ->description('Basic site identity and catalogue behaviour.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('site_tagline')
                            ->label('Tagline')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('catalogue_per_page')
                            ->label('Items per catalogue page')
                            ->numeric()
                            ->required()
                            ->minValue(12)
                            ->maxValue(60)
                            ->helperText('How many movies or series appear on each browse page.'),
                    ]),
                Section::make('Movie sync')
                    ->description('TMDB pages to fetch per category when running a media sync (~20 titles per page).')
                    ->columns(2)
                    ->schema([
                        $this->tmdbPageField('movie_popular_pages', 'Popular'),
                        $this->tmdbPageField('movie_now_playing_pages', 'Now playing'),
                        $this->tmdbPageField('movie_upcoming_pages', 'Upcoming'),
                        $this->tmdbPageField('movie_top_rated_pages', 'Top rated'),
                    ]),
                Section::make('Series sync')
                    ->columns(2)
                    ->schema([
                        $this->tmdbPageField('tv_popular_pages', 'Popular'),
                        $this->tmdbPageField('tv_on_the_air_pages', 'On the air'),
                        $this->tmdbPageField('tv_top_rated_pages', 'Top rated'),
                        $this->tmdbPageField('tv_airing_today_pages', 'Airing today'),
                    ]),
                Section::make('Actors sync')
                    ->schema([
                        $this->tmdbPageField('person_popular_pages', 'Popular people'),
                    ]),
                Section::make('Search')
                    ->description('How many TMDB search result pages to load.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('search_autocomplete_pages')
                            ->label('Navbar autocomplete pages')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(SiteSettingsData::MAX_TMDB_PAGES),
                        TextInput::make('search_full_pages')
                            ->label('Full search page pages')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(SiteSettingsData::MAX_TMDB_PAGES),
                    ]),
                Section::make('Home page')
                    ->description('How many items appear in each section on the home page.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('home_hero_limit')
                            ->label('Hero carousel (trending)')
                            ->numeric()
                            ->required()
                            ->minValue(3)
                            ->maxValue(30)
                            ->helperText('Trending movies & series slides in the hero.'),
                        TextInput::make('home_recommendations_limit')
                            ->label('Recommended for you')
                            ->numeric()
                            ->required()
                            ->minValue(4)
                            ->maxValue(48),
                        TextInput::make('home_actor_feed_limit')
                            ->label('From actors you follow')
                            ->numeric()
                            ->required()
                            ->minValue(4)
                            ->maxValue(36)
                            ->helperText('Personalized suggestions from followed actors.'),
                        TextInput::make('home_featured_lists_limit')
                            ->label('Featured list cards')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(12)
                            ->helperText('How many curated list cards to show on the home page.'),
                    ]),
                Section::make('Features & limits')
                    ->columns(2)
                    ->schema([
                        Toggle::make('enable_recommendations')->label('Personalized recommendations'),
                        Toggle::make('enable_actor_feed')->label('Actor follow feed'),
                        Toggle::make('enable_public_lists')->label('Public curated lists'),
                        Toggle::make('enable_payments')->label('Require subscription for playback'),
                        Toggle::make('site_wide_autoplay')->label('Autoplay trailers site-wide default'),
                        Toggle::make('maintenance_mode')->label('Maintenance mode'),
                        TextInput::make('tmdb_daily_request_limit')
                            ->label('TMDB daily request budget')
                            ->numeric()
                            ->required()
                            ->minValue(100)
                            ->maxValue(50000),
                        TextInput::make('hero_pinned_ids')
                            ->label('Hero pinned IDs')
                            ->placeholder('movie:550,tv:1399')
                            ->helperText('Comma-separated type:id pairs to prioritize in the hero carousel.')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync')
                ->label('Run sync now')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('This runs flexter:fetch using the page counts above. It may take several minutes.')
                ->action(function (): void {
                    Artisan::call('flexter:fetch');

                    Notification::make()
                        ->success()
                        ->title('Media sync completed')
                        ->body('Check TMDB API Activity for details.')
                        ->send();
                }),
            Action::make('save')
                ->label('Save settings')
                ->action('save'),
        ];
    }

    public function save(SiteSettingsServiceInterface $siteSettings): void
    {
        $siteSettings->update($this->form->getState());

        HomeCache::bust();

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->send();
    }

    private function tmdbPageField(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->numeric()
            ->required()
            ->minValue(1)
            ->maxValue(SiteSettingsData::MAX_TMDB_PAGES)
            ->helperText('1–'.SiteSettingsData::MAX_TMDB_PAGES.' pages');
    }
}
