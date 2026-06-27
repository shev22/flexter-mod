<?php

namespace App\Filament\Pages;

use App\Shared\Data\PlaybackSettingsData;
use App\Site\Services\Interfaces\SiteSettingsServiceInterface;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PlaybackSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Streaming Player';

    protected static ?string $title = 'Streaming Player';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.playback-settings';

    /** @var array<string, mixed> */
    public array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function mount(SiteSettingsServiceInterface $siteSettings): void
    {
        $this->form->fill(PlaybackSettingsData::fromArray($siteSettings->getPayload())->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Provider')
                    ->description('VidPlus is the recommended provider. VidPhantom offers TMDB embeds with postMessage progress. VidSrc remains available as a fallback.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('playback_enabled')
                            ->label('Enable streaming')
                            ->columnSpanFull(),
                        Select::make('playback_provider')
                            ->label('Provider')
                            ->options([
                                'vidplus' => 'VidPlus (vidplus.to)',
                                'vidphantom' => 'VidPhantom (vidphantom.com)',
                                'vidsrc' => 'VidSrc (vidsrc.to / vidsrc.ru)',
                                'disabled' => 'Disabled',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (string $state, Set $set): void {
                                $set('playback_base_url', match ($state) {
                                    'vidphantom' => 'https://vidphantom.com',
                                    'vidsrc' => 'https://vidsrc.to',
                                    'vidplus' => 'https://player.vidplus.to',
                                    default => 'https://player.vidplus.to',
                                });

                                if ($state === 'vidphantom') {
                                    $set('playback_progress_mode', 'postmessage');
                                }
                            }),
                        TextInput::make('playback_base_url')
                            ->label('Player base URL')
                            ->required()
                            ->placeholder('https://player.vidplus.to')
                            ->helperText('VidPlus: https://player.vidplus.to — VidPhantom: https://vidphantom.com — VidSrc: https://vidsrc.to'),
                        Select::make('playback_progress_mode')
                            ->label('Progress tracking')
                            ->options([
                                'session' => 'Session time estimate',
                                'postmessage' => 'Provider postMessage (VidSrc.ru / VidPhantom)',
                            ])
                            ->required()
                            ->helperText(fn (Get $get): string => $get('playback_provider') === 'vidphantom'
                                ? 'VidPhantom uses postMessage for accurate progress — enabled automatically.'
                                : 'Use postMessage for vidsrc.ru or VidPhantom embeds.'),
                        Select::make('playback_url_style')
                            ->label('VidSrc URL style')
                            ->options([
                                'embed' => 'Embed (/embed/movie/{id})',
                                'legacy' => 'Legacy query string (vidsrc.ru)',
                            ])
                            ->visible(fn (Get $get): bool => $get('playback_provider') === 'vidsrc'),
                        TextInput::make('playback_server')
                            ->label('Preferred server')
                            ->placeholder('minecloud or 3')
                            ->helperText('VidPlus only — server name or number.')
                            ->visible(fn (Get $get): bool => $get('playback_provider') === 'vidplus'),
                    ]),
                Section::make('Playback controls')
                    ->description(fn (Get $get): string => match ($get('playback_provider')) {
                        'vidphantom' => 'VidPhantom parameters: autoplay, poster, nextbutton, startAt (resume).',
                        'vidsrc' => 'VidSrc playback options are limited — most settings apply to VidPlus/VidPhantom only.',
                        default => 'VidPlus parameters: autoplay, autonext, nextbutton, progress (resume).',
                    })
                    ->columns(2)
                    ->schema([
                        Toggle::make('playback_autoplay')->label('Autoplay'),
                        Toggle::make('playback_autonext')
                            ->label('Auto next episode (TV)')
                            ->visible(fn (Get $get): bool => $get('playback_provider') === 'vidplus'),
                        Toggle::make('playback_next_button')->label('Show next episode button'),
                        Toggle::make('playback_resume_from_progress')->label('Resume from watch history'),
                        Toggle::make('playback_sync_accent_color')
                            ->label('Match site accent as primary color')
                            ->columnSpanFull(),
                    ]),
                Section::make('Visual theme')
                    ->description(fn (Get $get): string => $get('playback_provider') === 'vidphantom'
                        ? 'VidPhantom hex colors without # — primaryColor, accentColor, secondaryColor, iconColor.'
                        : 'Hex colors without # — primarycolor, secondarycolor, iconcolor.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('playback_primary_color')->label('Primary color')->placeholder('6C63FF'),
                        TextInput::make('playback_secondary_color')->label('Secondary color')->placeholder('9F9BFF'),
                        TextInput::make('playback_icon_color')->label('Icon color')->placeholder('FFFFFF'),
                        Toggle::make('playback_poster')->label('Show poster'),
                        Toggle::make('playback_show_title')
                            ->label('Show title')
                            ->visible(fn (Get $get): bool => $get('playback_provider') === 'vidplus'),
                        Select::make('playback_icons')
                            ->label('Icon style')
                            ->options(fn (Get $get): array => match ($get('playback_provider')) {
                                'vidphantom' => [
                                    'default' => 'Default',
                                    'minimal' => 'Minimal',
                                    'rounded' => 'Rounded',
                                ],
                                default => [
                                    'default' => 'Default',
                                    'netflix' => 'Netflix',
                                    'vid' => 'Vid',
                                    'lucide' => 'Lucide',
                                    'tb' => 'TB',
                                ],
                            }),
                        TextInput::make('playback_logo_url')
                            ->label('Custom logo URL')
                            ->url()
                            ->placeholder('https://example.com/logo.png')
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => $get('playback_provider') === 'vidplus'),
                    ]),
                Section::make('Player UI')
                    ->columns(2)
                    ->visible(fn (Get $get): bool => $get('playback_provider') === 'vidplus')
                    ->schema([
                        Toggle::make('playback_server_icon')->label('Server selection icon'),
                        Toggle::make('playback_setting')->label('Settings icon'),
                        Toggle::make('playback_pip')->label('Picture-in-picture'),
                        Toggle::make('playback_episode_list')->label('Episode list (TV)'),
                        Toggle::make('playback_chromecast')->label('Chromecast'),
                        Toggle::make('playback_watchparty')->label('WatchParty'),
                        Toggle::make('playback_download')->label('Download button'),
                    ]),
                Section::make('Subtitles & font')
                    ->columns(2)
                    ->visible(fn (Get $get): bool => $get('playback_provider') === 'vidplus')
                    ->schema([
                        TextInput::make('playback_font')->label('Font family')->placeholder('Roboto'),
                        TextInput::make('playback_font_color')->label('Font color (hex)')->placeholder('FFFFFF'),
                        TextInput::make('playback_font_size')
                            ->label('Font size (px)')
                            ->numeric()
                            ->minValue(10)
                            ->maxValue(48),
                        TextInput::make('playback_font_opacity')
                            ->label('Font background opacity (0–1)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1)
                            ->step(0.1),
                    ]),
                Section::make('Cinema mode — hide in-player settings')
                    ->description('Hide VidPlus built-in customization controls for a cleaner embed.')
                    ->columns(3)
                    ->collapsed()
                    ->visible(fn (Get $get): bool => $get('playback_provider') === 'vidplus')
                    ->schema([
                        Toggle::make('playback_hide_primary_color')->label('Hide primary color'),
                        Toggle::make('playback_hide_secondary_color')->label('Hide secondary color'),
                        Toggle::make('playback_hide_icon_color')->label('Hide icon color'),
                        Toggle::make('playback_hide_progress_control')->label('Hide progress control'),
                        Toggle::make('playback_hide_icon_set')->label('Hide icon set'),
                        Toggle::make('playback_hide_autonext')->label('Hide auto-next'),
                        Toggle::make('playback_hide_autoplay')->label('Hide autoplay'),
                        Toggle::make('playback_hide_next_button')->label('Hide next button'),
                        Toggle::make('playback_hide_poster')->label('Hide poster toggle'),
                        Toggle::make('playback_hide_title')->label('Hide title toggle'),
                        Toggle::make('playback_hide_chromecast')->label('Hide Chromecast toggle'),
                        Toggle::make('playback_hide_episode_list')->label('Hide episode list toggle'),
                        Toggle::make('playback_hide_server_icon')->label('Hide server icon toggle'),
                        Toggle::make('playback_hide_pip')->label('Hide PIP toggle'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cinema_preset')
                ->label('Apply cinema preset')
                ->color('gray')
                ->action(function (): void {
                    $this->data = array_merge($this->data, [
                        'playback_poster' => false,
                        'playback_show_title' => false,
                        'playback_chromecast' => false,
                        'playback_pip' => false,
                        'playback_episode_list' => false,
                        'playback_server_icon' => false,
                        'playback_watchparty' => false,
                        'playback_hide_poster' => true,
                        'playback_hide_title' => true,
                        'playback_hide_chromecast' => true,
                        'playback_hide_pip' => true,
                        'playback_hide_episode_list' => true,
                        'playback_hide_server_icon' => true,
                    ]);
                    $this->form->fill($this->data);

                    Notification::make()
                        ->success()
                        ->title('Cinema preset applied')
                        ->body('Review and save to persist.')
                        ->send();
                }),
            Action::make('save')
                ->label('Save player settings')
                ->action('save'),
        ];
    }

    public function save(SiteSettingsServiceInterface $siteSettings): void
    {
        $playback = PlaybackSettingsData::fromArray($this->form->getState());
        $siteSettings->update($playback->toArray());

        Notification::make()
            ->success()
            ->title('Player settings saved')
            ->send();
    }
}
