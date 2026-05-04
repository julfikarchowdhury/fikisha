<?php

namespace Database\Seeders\Backend\FrontWeb;

use App\Enums\Status;
use App\Models\Backend\FrontWeb\Page;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    private const PLACEHOLDER = '<p>Please update this content from the admin panel.</p>';

    /**
     * Ensure privacy policy and terms & conditions pages exist (idempotent).
     */
    public function run(): void
    {
        $this->seedPage(
            'privacy_policy',
            'Privacy Policy',
        );

        $this->seedPage(
            'terms_conditions',
            'Terms & Conditions',
        );
    }

    private function seedPage(string $slug, string $title): void
    {
        $page = Page::query()->firstOrNew(['page' => $slug]);
        $page->title = $title;
        if (empty($page->description)) {
            $page->description = self::PLACEHOLDER;
        }
        $page->status = Status::ACTIVE;
        $page->save();
    }
}
