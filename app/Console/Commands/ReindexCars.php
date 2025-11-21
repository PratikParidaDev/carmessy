<?

namespace App\Console\Commands;

use App\Models\Car;
use Illuminate\Console\Command;

class ReindexCars extends Command
{
    protected $signature = 'cars:reindex';
    protected $description = 'Reindex all cars for search';

    public function handle()
    {
        $this->info('Reindexing cars...');

        Car::approved()->published()->searchable();

        $this->info('Cars reindexed successfully!');
        
        return Command::SUCCESS;
    }
}



?>