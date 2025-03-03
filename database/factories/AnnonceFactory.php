<?php
namespace Database\Factories;

use App\Models\Annonce;
use App\Models\Equipement;
use App\Models\TypeDeLogement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Annonce>
 */
class AnnonceFactory extends Factory
{
    protected $model = Annonce::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role_id' => 3]), // Proprietaire role (role_id 3)
            'type_de_logement_id' => TypeDeLogement::factory(),
            'location' => $this->faker->city . ', ' . $this->faker->country,
            'price' => $this->faker->randomFloat(2, 50, 500), // Price between 50.00 and 500.00
            'image' => null, // Optional: Can add fake image paths if needed
            'available_until' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'), // Future date within 1 year
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => $this->faker->dateTimeThisYear(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Annonce $annonce) {
            // Attach random equipment (1 to 3 items)
            $equipements = Equipement::all()->random(rand(1, 3))->pluck('id')->toArray();
            $annonce->equipements()->sync($equipements);
        });
    }
}