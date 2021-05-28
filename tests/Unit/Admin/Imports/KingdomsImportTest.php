<?php

namespace Tests\Unit\Admin\Imports;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use App\Admin\Import\Kingdoms\KingdomsImport;

class KingdomsImportTest extends TestCase {
    use RefreshDatabase;

    public function testImport() {
        Excel::import(new KingdomsImport(), resource_path('data-imports/kingdoms.xlsx'));

        $this->assertTrue(true);
    }
}
