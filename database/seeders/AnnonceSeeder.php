namespace Database\Seeders;

use App\Models\Annonce;
use App\Models\Equipement;
use App\Models\Role;
use App\Models\TypeDeLogement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnonceSeeder extends Seeder
{
    public function run(): void
    {
        Annonce::factory()->count(30)->create([
            'user_id' => $proprietaire->id,
        ]);
    }
}