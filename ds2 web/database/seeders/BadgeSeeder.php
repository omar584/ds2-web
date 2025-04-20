<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run()
    {
        $badges = [
            [
                'name' => 'Débutant',
                'description' => 'Créez votre premier objectif',
                'icon' => 'bi-stars',
                'category' => 'progress',
                'required_points' => 1
            ],
            [
                'name' => 'Planificateur',
                'description' => 'Créez 5 objectifs',
                'icon' => 'bi-calendar-check',
                'category' => 'progress',
                'required_points' => 5
            ],
            [
                'name' => 'Maître des objectifs',
                'description' => 'Créez 20 objectifs',
                'icon' => 'bi-trophy',
                'category' => 'progress',
                'required_points' => 20
            ],
            [
                'name' => 'Premier succès',
                'description' => 'Complétez votre premier objectif',
                'icon' => 'bi-check-circle',
                'category' => 'achievement',
                'required_points' => 1
            ],
            [
                'name' => 'Sur la bonne voie',
                'description' => 'Complétez 5 objectifs',
                'icon' => 'bi-graph-up',
                'category' => 'achievement',
                'required_points' => 5
            ],
            [
                'name' => 'Expert',
                'description' => 'Complétez 20 objectifs',
                'icon' => 'bi-award',
                'category' => 'achievement',
                'required_points' => 20
            ],
            [
                'name' => 'Globe-trotter',
                'description' => 'Ajoutez des objectifs dans 5 lieux différents',
                'icon' => 'bi-globe',
                'category' => 'location',
                'required_points' => 5
            ],
            [
                'name' => 'Influenceur',
                'description' => 'Partagez 5 objectifs publiquement',
                'icon' => 'bi-share',
                'category' => 'social',
                'required_points' => 5
            ],
            [
                'name' => 'Ponctuel',
                'description' => 'Complétez 5 objectifs avant leur deadline',
                'icon' => 'bi-clock-history',
                'category' => 'time',
                'required_points' => 5
            ]
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
} 