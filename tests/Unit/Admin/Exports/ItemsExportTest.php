<?php

namespace Tests\Unit\Admin\Exports;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use App\Admin\Exports\Items\ItemsExport;

class ItemsExportTest extends TestCase {
    use RefreshDatabase;

    public function testExport() {
        Excel::store(new ItemsExport, 'test.xlsx');

        Storage::disk('local')->assertExists('test.xlsx');

        Storage::disk('local')->delete('test.xlsx');
    }
}
