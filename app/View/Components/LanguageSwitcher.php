<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LanguageSwitcher extends Component
{
    /**
     * Language code mapping (WPML code => Display code).
     * WPML uses: en, ka-ge, ru
     */
    protected array $codeMap = [
        'ka-ge' => 'GE',
        'ka' => 'GE',
        'ru' => 'RU',
        'en' => 'EN',
    ];

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Check if WPML is active.
     */
    public function isWpmlActive(): bool
    {
        return function_exists('icl_get_languages');
    }

    /**
     * Get available languages from WPML.
     */
    public function languages(): array
    {
        if (!$this->isWpmlActive()) {
            return $this->getFallbackLanguages();
        }

        $wpmlLanguages = icl_get_languages('skip_missing=0&orderby=custom');

        if (empty($wpmlLanguages)) {
            return $this->getFallbackLanguages();
        }

        $languages = [];
        foreach ($wpmlLanguages as $code => $lang) {
            $languages[] = [
                'code' => $code,
                'display_code' => $this->getDisplayCode($code),
                'name' => $lang['native_name'] ?? $lang['translated_name'] ?? $code,
                'url' => $lang['url'] ?? '#',
                'active' => $lang['active'] ?? false,
                'flag' => $lang['country_flag_url'] ?? '',
            ];
        }

        return $languages;
    }

    /**
     * Get fallback languages when WPML is not active.
     * Uses WPML language codes: en, ka-ge, ru
     */
    protected function getFallbackLanguages(): array
    {
        return [
            [
                'code' => 'ka-ge',
                'display_code' => 'GE',
                'name' => 'ქართული',
                'url' => '#',
                'active' => true,
                'flag' => '',
            ],
            [
                'code' => 'ru',
                'display_code' => 'RU',
                'name' => 'Русский',
                'url' => '#',
                'active' => false,
                'flag' => '',
            ],
            [
                'code' => 'en',
                'display_code' => 'EN',
                'name' => 'English',
                'url' => '#',
                'active' => false,
                'flag' => '',
            ],
        ];
    }

    /**
     * Get display code for a language.
     */
    protected function getDisplayCode(string $code): string
    {
        return $this->codeMap[strtolower($code)] ?? strtoupper(substr($code, 0, 2));
    }

    /**
     * Get the current language.
     */
    public function currentLanguage(): array
    {
        $languages = $this->languages();

        foreach ($languages as $lang) {
            if ($lang['active']) {
                return $lang;
            }
        }

        // Fallback to first language
        return $languages[0] ?? [
            'code' => 'ka-ge',
            'display_code' => 'GE',
            'name' => 'ქართული',
            'url' => '#',
            'active' => true,
            'flag' => '',
        ];
    }

    /**
     * Get inactive languages (for dropdown).
     */
    public function otherLanguages(): array
    {
        return array_filter($this->languages(), fn($lang) => !$lang['active']);
    }

    /**
     * Check if there are multiple languages available.
     */
    public function hasMultipleLanguages(): bool
    {
        return count($this->languages()) > 1;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.language-switcher');
    }
}
