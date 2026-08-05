<?php

namespace App\Services;

use App\Models\User;

class ThemeService
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function themes(): array
    {
        return [
            'neon_arcade_dark' => [
                'label' => 'Neon Arcade',
                'family' => 'neon_arcade',
                'family_label' => 'Neon Arcade',
                'mode' => 'dark',
                'swatch' => '#00f0ff',
                'variables' => [
                    '--theme-bg' => '#080b14',
                    '--theme-surface' => 'rgba(12, 17, 30, 0.88)',
                    '--theme-navbar' => 'rgba(8, 11, 20, 0.85)',
                    '--theme-text' => '#f3f7ff',
                    '--theme-muted' => '#9ba9c7',
                    '--theme-accent' => '#00f0ff',
                    '--theme-accent-soft' => '#8b5cf6',
                    '--theme-border' => 'rgba(0, 240, 255, 0.20)',
                    '--theme-glow' => '0 0 28px rgba(0, 240, 255, 0.26)',
                ],
            ],
            'neon_arcade_light' => [
                'label' => 'Neon Arcade',
                'family' => 'neon_arcade',
                'family_label' => 'Neon Arcade',
                'mode' => 'light',
                'swatch' => '#00f0ff',
                'variables' => [
                    '--theme-bg' => '#f0fbfd',
                    '--theme-surface' => 'rgba(255, 255, 255, 0.90)',
                    '--theme-navbar' => 'rgba(240, 251, 253, 0.92)',
                    '--theme-text' => '#0b1a22',
                    '--theme-muted' => '#4b6572',
                    '--theme-accent' => '#0891b2',
                    '--theme-accent-soft' => '#7c3aed',
                    '--theme-border' => 'rgba(8, 145, 178, 0.22)',
                    '--theme-glow' => '0 20px 60px rgba(8, 145, 178, 0.16)',
                ],
            ],
            'ember_dark' => [
                'label' => 'Ember',
                'family' => 'ember',
                'family_label' => 'Ember',
                'mode' => 'dark',
                'swatch' => '#ff6a00',
                'variables' => [
                    '--theme-bg' => '#0d0800',
                    '--theme-surface' => 'rgba(20, 12, 4, 0.88)',
                    '--theme-navbar' => 'rgba(13, 8, 0, 0.85)',
                    '--theme-text' => '#fff3e2',
                    '--theme-muted' => '#c4a68f',
                    '--theme-accent' => '#ff6a00',
                    '--theme-accent-soft' => '#ffb347',
                    '--theme-border' => 'rgba(255, 106, 0, 0.20)',
                    '--theme-glow' => '0 0 28px rgba(255, 106, 0, 0.26)',
                ],
            ],
            'ember_light' => [
                'label' => 'Ember',
                'family' => 'ember',
                'family_label' => 'Ember',
                'mode' => 'light',
                'swatch' => '#ff6a00',
                'variables' => [
                    '--theme-bg' => '#fff8f0',
                    '--theme-surface' => 'rgba(255, 255, 255, 0.88)',
                    '--theme-navbar' => 'rgba(255, 248, 240, 0.90)',
                    '--theme-text' => '#3a2215',
                    '--theme-muted' => '#6f5847',
                    '--theme-accent' => '#e85400',
                    '--theme-accent-soft' => '#fbbf24',
                    '--theme-border' => 'rgba(232, 84, 0, 0.20)',
                    '--theme-glow' => '0 20px 60px rgba(232, 84, 0, 0.16)',
                ],
            ],
        ];
    }

    /**
     * Grouped by color family, for a family-picker + light/dark toggle UI.
     *
     * @return array<string, array<string, string>>
     */
    public function families(): array
    {
        return [
            'neon_arcade' => [
                'label' => 'Neon Arcade',
                'swatch' => '#00f0ff',
                'dark_key' => 'neon_arcade_dark',
                'light_key' => 'neon_arcade_light',
            ],
            'ember' => [
                'label' => 'Ember',
                'swatch' => '#ff6a00',
                'dark_key' => 'ember_dark',
                'light_key' => 'ember_light',
            ],
        ];
    }

    public function current(?User $user = null): array
    {
        $key = $user?->theme ?? 'neon_arcade_dark';

        if (! array_key_exists($key, $this->themes())) {
            $key = 'neon_arcade_dark';
        }

        $theme = $this->themes()[$key];
        $theme['key'] = $key;
        $theme['style'] = $this->styleString($theme['variables']);

        return $theme;
    }

    /**
     * @return array<string, string>
     */
    public function options(): array
    {
        return collect($this->themes())
            ->mapWithKeys(fn (array $theme, string $key) => [$key => $theme['label']])
            ->all();
    }

    /**
     * @param array<string, string> $variables
     */
    public function styleString(array $variables): string
    {
        return collect($variables)
            ->map(fn (string $value, string $key) => $key.': '.$value)
            ->implode('; ');
    }
}
