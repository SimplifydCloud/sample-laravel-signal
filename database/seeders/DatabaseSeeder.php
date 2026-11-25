<?php

namespace Database\Seeders;

use App\Models\Idea;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $ideas = [
            ['A little more dark mode, please', 'For the late-night makers. A thoughtful dark theme that is easy on the eyes and feels just as good as daylight.', 'Experience', 'Building', 128],
            ['Connect the tools we already love', 'Bring project updates into the team chat so every launch, little win, and useful detail finds the right people.', 'Integrations', 'Exploring', 96],
            ['Make the first five minutes magical', 'A guided welcome with a real demo project, helpful suggestions, and one small win before the first coffee gets cold.', 'Experience', 'Shipped', 84],
            ['A faster way from idea to live', 'Trim the waiting time between pushing a change and seeing it in the world. A faster feedback loop for everyone.', 'Performance', 'Building', 73],
            ['Give every project its own personality', 'Let us choose a project colour and a tiny icon. A small touch that makes a busy workspace easier to navigate.', 'Experience', 'Exploring', 61],
            ['Preview every possibility', 'Share a preview with the whole crew before a change goes live. Great work gets better when everyone can try it.', 'Integrations', 'Shipped', 52],
        ];
        foreach ($ideas as [$title, $description, $category, $status, $votes]) {
            Idea::firstOrCreate(['title' => $title], compact('description', 'category', 'status', 'votes'));
        }
    }
}
