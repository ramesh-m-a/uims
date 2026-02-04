<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeMaster extends Command
{
    /**
     * Signature:
     * make:master common Gender mas_gender
     */
    protected $signature = 'make:master {type} {name} {table}';

    protected $description = 'Generate Master/Common CRUD (Livewire + Config + View)';

    public function handle(): int
    {
        $type  = strtolower($this->argument('type'));
        $name  = Str::studly($this->argument('name')); // Gender
        $table = $this->argument('table');             // mas_gender
        $slug  = Str::kebab($name);                     // gender

        if ($type !== 'common') {
            $this->error('❌ Only Master/Common supported.');
            return self::FAILURE;
        }

        $this->info("🚀 Generating Master/Common: {$name}");

        $this->generateModel($name, $table);
        $this->generateLivewire($name);
        $this->generateConfig($slug);
        $this->appendRoute($slug);

        $this->info("✅ Master/Common {$name} generated successfully");
        return self::SUCCESS;
    }

    /* ===============================
     | MODEL
     =============================== */
    protected function generateModel(string $name, string $table): void
    {
        $path = app_path("Models/Master/Common/{$name}.php");

        if (File::exists($path)) {
            $this->warn("⚠️ Model already exists: {$name}");
            return;
        }

        File::ensureDirectoryExists(dirname($path));

        File::put($path, <<<PHP
<?php

namespace App\Models\Master\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class {$name} extends Model
{
    use SoftDeletes;

    protected \$table = '{$table}';

    protected \$fillable = [
        '{$table}_name',
        '{$table}_status_id',
    ];
}
PHP);
    }

    /* ===============================
     | LIVEWIRE (uses stub)
     =============================== */
    protected function generateLivewire(string $name): void
    {
        $component = "master.common.{$name}.{$name}Table";
        $this->call('make:livewire', ['name' => $component, '--stub' => 'master/livewire.stub']);
    }

    /* ===============================
     | CONFIG
     =============================== */
    protected function generateConfig(string $slug): void
    {
        $path = config_path("master/common/{$slug}.php");

        if (File::exists($path)) {
            $this->warn("⚠️ Config already exists: {$slug}");
            return;
        }

        File::ensureDirectoryExists(dirname($path));

        File::put($path, <<<PHP
<?php

return [
    'status_field' => 'mas_{$slug}_status_id',

    'columns' => [
        'mas_{$slug}_name' => [
            'label' => ucfirst('{$slug}'),
            'sortable' => true,
            'filterable' => true,
            'order' => 1,
        ],

        'mas_{$slug}_status_id' => [
            'label' => 'Status',
            'type'  => 'enum',
            'options' => [
                1 => 'Active',
                2 => 'In Active',
            ],
            'order' => 99,
        ],
    ],
];
PHP);
    }

    /* ===============================
     | ROUTE
     =============================== */
    protected function appendRoute(string $slug): void
    {
        $route = <<<PHP

Route::middleware(['auth', 'permission:master.common.view'])
    ->prefix('master/common')
    ->as('master.common.')
    ->group(function () {
        Route::get('/{$slug}', \\App\\Livewire\\Master\\Common\\{$this->argument('name')}\\{$this->argument('name')}Table::class)
            ->name('{$slug}.index');
    });
PHP;

        File::append(base_path('routes/web.php'), $route);
    }
}
