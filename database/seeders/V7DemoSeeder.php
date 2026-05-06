<?php

namespace Database\Seeders;

use App\Modules\Platform\Services\MobileAppService;
use Illuminate\Database\Seeder;

class V7DemoSeeder extends Seeder
{
    public function run(): void
    {
        app(MobileAppService::class)->createDefaultConfig();
    }
}
