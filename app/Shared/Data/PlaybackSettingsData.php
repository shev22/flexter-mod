<?php

namespace App\Shared\Data;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

final class PlaybackSettingsData implements Arrayable, JsonSerializable
{
    public const PROVIDERS = ['vidplus', 'vidphantom', 'vidsrc', 'disabled'];

    public const ICON_STYLES = ['default', 'netflix', 'vid', 'lucide', 'tb'];

    public const VIDPHANTOM_ICON_STYLES = ['default', 'minimal', 'rounded'];

    public const PROGRESS_MODES = ['session', 'postmessage'];

    public const URL_STYLES = ['embed', 'legacy'];

    public function __construct(
        public bool $enabled,
        public string $provider,
        public string $baseUrl,
        public string $progressMode,
        public string $urlStyle,
        public bool $autoplay,
        public bool $autonext,
        public bool $nextButton,
        public bool $resumeFromProgress,
        public bool $syncAccentColor,
        public string $primaryColor,
        public string $secondaryColor,
        public string $iconColor,
        public bool $poster,
        public bool $showTitle,
        public string $icons,
        public bool $serverIcon,
        public bool $setting,
        public bool $pip,
        public bool $episodeList,
        public bool $chromecast,
        public bool $watchparty,
        public bool $download,
        public string $font,
        public string $fontColor,
        public int $fontSize,
        public float $opacity,
        public string $logoUrl,
        public string $server,
        public bool $hidePrimaryColor,
        public bool $hideSecondaryColor,
        public bool $hideIconColor,
        public bool $hideProgressControl,
        public bool $hideIconSet,
        public bool $hideAutonext,
        public bool $hideAutoplay,
        public bool $hideNextButton,
        public bool $hidePoster,
        public bool $hideTitle,
        public bool $hideChromecast,
        public bool $hideEpisodeList,
        public bool $hideServerIcon,
        public bool $hidePip,
    ) {}

    public static function defaults(): self
    {
        $config = config('flexter.playback', []);

        return self::fromArray(is_array($config) ? $config : []);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        $defaults = self::configDefaults();

        return new self(
            enabled: self::bool($payload['playback_enabled'] ?? $payload['enabled'] ?? null, $defaults->enabled),
            provider: self::enum(
                $payload['playback_provider'] ?? $payload['provider'] ?? null,
                self::PROVIDERS,
                $defaults->provider,
            ),
            baseUrl: rtrim((string) ($payload['playback_base_url'] ?? $payload['base_url'] ?? $defaults->baseUrl), '/'),
            progressMode: self::enum(
                $payload['playback_progress_mode'] ?? $payload['progress_mode'] ?? null,
                self::PROGRESS_MODES,
                $defaults->progressMode,
            ),
            urlStyle: self::enum(
                $payload['playback_url_style'] ?? $payload['url_style'] ?? null,
                self::URL_STYLES,
                $defaults->urlStyle,
            ),
            autoplay: self::bool($payload['playback_autoplay'] ?? null, $defaults->autoplay),
            autonext: self::bool($payload['playback_autonext'] ?? null, $defaults->autonext),
            nextButton: self::bool($payload['playback_next_button'] ?? null, $defaults->nextButton),
            resumeFromProgress: self::bool($payload['playback_resume_from_progress'] ?? null, $defaults->resumeFromProgress),
            syncAccentColor: self::bool($payload['playback_sync_accent_color'] ?? null, $defaults->syncAccentColor),
            primaryColor: self::hex($payload['playback_primary_color'] ?? null, $defaults->primaryColor),
            secondaryColor: self::hex($payload['playback_secondary_color'] ?? null, $defaults->secondaryColor),
            iconColor: self::hex($payload['playback_icon_color'] ?? null, $defaults->iconColor),
            poster: self::bool($payload['playback_poster'] ?? null, $defaults->poster),
            showTitle: self::bool($payload['playback_show_title'] ?? null, $defaults->showTitle),
            icons: self::enum($payload['playback_icons'] ?? null, self::ICON_STYLES, $defaults->icons),
            serverIcon: self::bool($payload['playback_server_icon'] ?? null, $defaults->serverIcon),
            setting: self::bool($payload['playback_setting'] ?? null, $defaults->setting),
            pip: self::bool($payload['playback_pip'] ?? null, $defaults->pip),
            episodeList: self::bool($payload['playback_episode_list'] ?? null, $defaults->episodeList),
            chromecast: self::bool($payload['playback_chromecast'] ?? null, $defaults->chromecast),
            watchparty: self::bool($payload['playback_watchparty'] ?? null, $defaults->watchparty),
            download: self::bool($payload['playback_download'] ?? null, $defaults->download),
            font: (string) ($payload['playback_font'] ?? $defaults->font),
            fontColor: self::hex($payload['playback_font_color'] ?? null, $defaults->fontColor),
            fontSize: self::int($payload['playback_font_size'] ?? null, $defaults->fontSize, 10, 48),
            opacity: self::float($payload['playback_font_opacity'] ?? null, $defaults->opacity, 0, 1),
            logoUrl: (string) ($payload['playback_logo_url'] ?? $defaults->logoUrl),
            server: (string) ($payload['playback_server'] ?? $defaults->server),
            hidePrimaryColor: self::bool($payload['playback_hide_primary_color'] ?? null, $defaults->hidePrimaryColor),
            hideSecondaryColor: self::bool($payload['playback_hide_secondary_color'] ?? null, $defaults->hideSecondaryColor),
            hideIconColor: self::bool($payload['playback_hide_icon_color'] ?? null, $defaults->hideIconColor),
            hideProgressControl: self::bool($payload['playback_hide_progress_control'] ?? null, $defaults->hideProgressControl),
            hideIconSet: self::bool($payload['playback_hide_icon_set'] ?? null, $defaults->hideIconSet),
            hideAutonext: self::bool($payload['playback_hide_autonext'] ?? null, $defaults->hideAutonext),
            hideAutoplay: self::bool($payload['playback_hide_autoplay'] ?? null, $defaults->hideAutoplay),
            hideNextButton: self::bool($payload['playback_hide_next_button'] ?? null, $defaults->hideNextButton),
            hidePoster: self::bool($payload['playback_hide_poster'] ?? null, $defaults->hidePoster),
            hideTitle: self::bool($payload['playback_hide_title'] ?? null, $defaults->hideTitle),
            hideChromecast: self::bool($payload['playback_hide_chromecast'] ?? null, $defaults->hideChromecast),
            hideEpisodeList: self::bool($payload['playback_hide_episode_list'] ?? null, $defaults->hideEpisodeList),
            hideServerIcon: self::bool($payload['playback_hide_server_icon'] ?? null, $defaults->hideServerIcon),
            hidePip: self::bool($payload['playback_hide_pip'] ?? null, $defaults->hidePip),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'playback_enabled' => $this->enabled,
            'playback_provider' => $this->provider,
            'playback_base_url' => $this->baseUrl,
            'playback_progress_mode' => $this->progressMode,
            'playback_url_style' => $this->urlStyle,
            'playback_autoplay' => $this->autoplay,
            'playback_autonext' => $this->autonext,
            'playback_next_button' => $this->nextButton,
            'playback_resume_from_progress' => $this->resumeFromProgress,
            'playback_sync_accent_color' => $this->syncAccentColor,
            'playback_primary_color' => $this->primaryColor,
            'playback_secondary_color' => $this->secondaryColor,
            'playback_icon_color' => $this->iconColor,
            'playback_poster' => $this->poster,
            'playback_show_title' => $this->showTitle,
            'playback_icons' => $this->icons,
            'playback_server_icon' => $this->serverIcon,
            'playback_setting' => $this->setting,
            'playback_pip' => $this->pip,
            'playback_episode_list' => $this->episodeList,
            'playback_chromecast' => $this->chromecast,
            'playback_watchparty' => $this->watchparty,
            'playback_download' => $this->download,
            'playback_font' => $this->font,
            'playback_font_color' => $this->fontColor,
            'playback_font_size' => $this->fontSize,
            'playback_font_opacity' => $this->opacity,
            'playback_logo_url' => $this->logoUrl,
            'playback_server' => $this->server,
            'playback_hide_primary_color' => $this->hidePrimaryColor,
            'playback_hide_secondary_color' => $this->hideSecondaryColor,
            'playback_hide_icon_color' => $this->hideIconColor,
            'playback_hide_progress_control' => $this->hideProgressControl,
            'playback_hide_icon_set' => $this->hideIconSet,
            'playback_hide_autonext' => $this->hideAutonext,
            'playback_hide_autoplay' => $this->hideAutoplay,
            'playback_hide_next_button' => $this->hideNextButton,
            'playback_hide_poster' => $this->hidePoster,
            'playback_hide_title' => $this->hideTitle,
            'playback_hide_chromecast' => $this->hideChromecast,
            'playback_hide_episode_list' => $this->hideEpisodeList,
            'playback_hide_server_icon' => $this->hideServerIcon,
            'playback_hide_pip' => $this->hidePip,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toSharedArray(): array
    {
        return [
            'enabled' => $this->enabled,
            'provider' => $this->provider,
            'base_url' => $this->baseUrl,
            'progress_mode' => $this->progressMode,
            'url_style' => $this->urlStyle,
            'autoplay' => $this->autoplay,
            'autonext' => $this->autonext,
            'next_button' => $this->nextButton,
            'resume_from_progress' => $this->resumeFromProgress,
            'sync_accent_color' => $this->syncAccentColor,
            'primary_color' => $this->primaryColor,
            'secondary_color' => $this->secondaryColor,
            'icon_color' => $this->iconColor,
            'poster' => $this->poster,
            'show_title' => $this->showTitle,
            'icons' => $this->icons,
            'server_icon' => $this->serverIcon,
            'setting' => $this->setting,
            'pip' => $this->pip,
            'episode_list' => $this->episodeList,
            'chromecast' => $this->chromecast,
            'watchparty' => $this->watchparty,
            'download' => $this->download,
            'font' => $this->font,
            'font_color' => $this->fontColor,
            'font_size' => $this->fontSize,
            'font_opacity' => $this->opacity,
            'logo_url' => $this->logoUrl,
            'server' => $this->server,
            'hide_primary_color' => $this->hidePrimaryColor,
            'hide_secondary_color' => $this->hideSecondaryColor,
            'hide_icon_color' => $this->hideIconColor,
            'hide_progress_control' => $this->hideProgressControl,
            'hide_icon_set' => $this->hideIconSet,
            'hide_autonext' => $this->hideAutonext,
            'hide_autoplay' => $this->hideAutoplay,
            'hide_next_button' => $this->hideNextButton,
            'hide_poster' => $this->hidePoster,
            'hide_title' => $this->hideTitle,
            'hide_chromecast' => $this->hideChromecast,
            'hide_episode_list' => $this->hideEpisodeList,
            'hide_server_icon' => $this->hideServerIcon,
            'hide_pip' => $this->hidePip,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    private static function configDefaults(): self
    {
        return new self(
            enabled: filter_var(config('flexter.playback.enabled', true), FILTER_VALIDATE_BOOL),
            provider: (string) config('flexter.playback.provider', 'vidplus'),
            baseUrl: rtrim((string) config('flexter.playback.base_url', 'https://player.vidplus.to'), '/'),
            progressMode: (string) config('flexter.playback.progress_mode', 'session'),
            urlStyle: (string) config('flexter.playback.url_style', 'embed'),
            autoplay: filter_var(config('flexter.playback.autoplay', false), FILTER_VALIDATE_BOOL),
            autonext: filter_var(config('flexter.playback.autonext', true), FILTER_VALIDATE_BOOL),
            nextButton: filter_var(config('flexter.playback.next_button', true), FILTER_VALIDATE_BOOL),
            resumeFromProgress: filter_var(config('flexter.playback.resume_from_progress', true), FILTER_VALIDATE_BOOL),
            syncAccentColor: filter_var(config('flexter.playback.sync_accent_color', true), FILTER_VALIDATE_BOOL),
            primaryColor: (string) config('flexter.playback.primary_color', '6C63FF'),
            secondaryColor: (string) config('flexter.playback.secondary_color', '9F9BFF'),
            iconColor: (string) config('flexter.playback.icon_color', 'FFFFFF'),
            poster: filter_var(config('flexter.playback.poster', true), FILTER_VALIDATE_BOOL),
            showTitle: filter_var(config('flexter.playback.show_title', true), FILTER_VALIDATE_BOOL),
            icons: (string) config('flexter.playback.icons', 'default'),
            serverIcon: filter_var(config('flexter.playback.server_icon', true), FILTER_VALIDATE_BOOL),
            setting: filter_var(config('flexter.playback.setting', true), FILTER_VALIDATE_BOOL),
            pip: filter_var(config('flexter.playback.pip', true), FILTER_VALIDATE_BOOL),
            episodeList: filter_var(config('flexter.playback.episode_list', true), FILTER_VALIDATE_BOOL),
            chromecast: filter_var(config('flexter.playback.chromecast', true), FILTER_VALIDATE_BOOL),
            watchparty: filter_var(config('flexter.playback.watchparty', false), FILTER_VALIDATE_BOOL),
            download: filter_var(config('flexter.playback.download', false), FILTER_VALIDATE_BOOL),
            font: (string) config('flexter.playback.font', 'Roboto'),
            fontColor: (string) config('flexter.playback.font_color', 'FFFFFF'),
            fontSize: (int) config('flexter.playback.font_size', 20),
            opacity: (float) config('flexter.playback.font_opacity', 0.5),
            logoUrl: (string) config('flexter.playback.logo_url', ''),
            server: (string) config('flexter.playback.server', ''),
            hidePrimaryColor: false,
            hideSecondaryColor: false,
            hideIconColor: false,
            hideProgressControl: false,
            hideIconSet: false,
            hideAutonext: false,
            hideAutoplay: false,
            hideNextButton: false,
            hidePoster: false,
            hideTitle: false,
            hideChromecast: false,
            hideEpisodeList: false,
            hideServerIcon: false,
            hidePip: false,
        );
    }

    private static function bool(mixed $value, bool $default): bool
    {
        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    /**
     * @param  list<string>  $allowed
     */
    private static function enum(mixed $value, array $allowed, string $default): string
    {
        if (! is_string($value) || ! in_array($value, $allowed, true)) {
            return $default;
        }

        return $value;
    }

    private static function hex(mixed $value, string $default): string
    {
        if (! is_string($value) || $value === '') {
            return strtoupper(ltrim($default, '#'));
        }

        return strtoupper(ltrim($value, '#'));
    }

    private static function int(mixed $value, int $default, int $min, int $max): int
    {
        if (! is_numeric($value)) {
            return $default;
        }

        return max($min, min($max, (int) $value));
    }

    private static function float(mixed $value, float $default, float $min, float $max): float
    {
        if (! is_numeric($value)) {
            return $default;
        }

        return max($min, min($max, (float) $value));
    }
}
