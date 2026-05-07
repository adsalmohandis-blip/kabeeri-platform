<?php

namespace App\Filament\Pages;

use App\Support\Localization\KabeeriLocale;
use App\Support\Ui\AdminLocaleCopy;
use App\Support\Ui\KabeeriUiPreference;
use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class UserSettings extends EditProfile
{
    protected static ?string $title = 'User settings';

    public static function getLabel(): string
    {
        return AdminLocaleCopy::label('User settings');
    }

    public function getTitle(): string|Htmlable
    {
        return AdminLocaleCopy::label('User settings');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(AdminLocaleCopy::label('Account data'))
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                    ])
                    ->columns(2),
                Section::make(AdminLocaleCopy::label('Interface settings'))
                    ->schema([
                        $this->getAdminLocaleFormComponent(),
                        $this->getAdminThemeFormComponent(),
                        $this->getAdminFontFormComponent(),
                    ])
                    ->columns(3),
                Section::make(AdminLocaleCopy::label('Security'))
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ]),
            ]);
    }

    protected function getAdminLocaleFormComponent(): Component
    {
        return Select::make('admin_locale')
            ->label(AdminLocaleCopy::label('Admin language'))
            ->options($this->localeOptions())
            ->native(false)
            ->required();
    }

    protected function getAdminFontFormComponent(): Component
    {
        return Select::make('admin_font')
            ->label(AdminLocaleCopy::label('Admin font'))
            ->options($this->fontOptions())
            ->native(false)
            ->required();
    }

    protected function getAdminThemeFormComponent(): Component
    {
        return Select::make('admin_theme')
            ->label(AdminLocaleCopy::label('Admin theme'))
            ->options($this->themeOptions())
            ->native(false)
            ->required();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $metadata = $this->getUser()->profile?->metadata ?? [];

        $data['admin_locale'] = KabeeriLocale::normalize(
            session()->get(KabeeriLocale::contextSessionKey('admin'), $metadata['admin_locale'] ?? KabeeriLocale::default()),
        );
        $data['admin_font'] = KabeeriUiPreference::supportedFont(
            session()->get(KabeeriUiPreference::fontKey('admin'), $metadata['admin_font'] ?? config('kabeeri_ui_preferences.default_font')),
        );
        $data['admin_theme'] = KabeeriUiPreference::supportedTheme(
            session()->get(KabeeriUiPreference::themeKey('admin'), $metadata['admin_theme'] ?? config('kabeeri_ui_preferences.default_theme')),
        );

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $locale = KabeeriLocale::normalize($data['admin_locale'] ?? KabeeriLocale::default());
        $font = KabeeriUiPreference::supportedFont($data['admin_font'] ?? config('kabeeri_ui_preferences.default_font'));
        $theme = KabeeriUiPreference::supportedTheme($data['admin_theme'] ?? config('kabeeri_ui_preferences.default_theme'));

        session()->put(KabeeriLocale::contextSessionKey('admin'), $locale);
        session()->put(KabeeriLocale::sessionKey(), $locale);
        session()->put(KabeeriUiPreference::fontKey('admin'), $font);
        session()->put(KabeeriUiPreference::themeKey('admin'), $theme);

        $profile = $this->getUser()->profile()->firstOrCreate(
            ['user_id' => $this->getUser()->id],
            ['visibility' => 'private'],
        );

        $profile->forceFill([
            'metadata' => array_merge($profile->metadata ?? [], [
                'admin_locale' => $locale,
                'admin_font' => $font,
                'admin_theme' => $theme,
            ]),
        ])->save();

        unset($data['admin_locale'], $data['admin_font'], $data['admin_theme']);

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return AdminLocaleCopy::label('User settings saved');
    }

    /**
     * @return array<string, string>
     */
    private function localeOptions(): array
    {
        return collect(KabeeriLocale::supported())
            ->mapWithKeys(fn (array $language, string $locale): array => [
                $locale => "{$language['native_label']} ({$language['short_label']})",
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function fontOptions(): array
    {
        return collect(config('kabeeri_ui_preferences.fonts', []))
            ->mapWithKeys(function (array $font, string $key): array {
                $translationKey = 'kabeeri.ui.font_'.str_replace('-', '_', $key);

                return [$key => __($translationKey)];
            })
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function themeOptions(): array
    {
        return collect(config('kabeeri_ui_preferences.themes', []))
            ->mapWithKeys(fn (array $theme, string $key): array => [
                $key => __("kabeeri.ui.theme_mode_{$key}"),
            ])
            ->all();
    }
}
