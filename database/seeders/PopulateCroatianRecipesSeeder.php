<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Hash;

class PopulateCroatianRecipesSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * ✅ PROMIJENI OVO (kolege):
         * - Email korisnika kojem seedate recepte
         * - (Opcionalno) Password ako želite da se možete loginati s tim userom
         */
        $seedEmail = 'karla.fisic@gmail.com';        // <-- PROMIJENI
        $seedName  = 'Karla';            // <-- PROMIJENI
        $seedPass  = 'karla123';              // <-- PROMIJENI / ili obriši ako ne treba

        // Find or create user (ne briše ništa, samo osigura da user postoji)
        $user = User::firstOrCreate(
            ['email' => $seedEmail],
            [
                'name' => $seedName,
                'password' => Hash::make($seedPass),
                'email_verified_at' => now(),
            ]
        );

        /**
         * ✅ PROMIJENI OVO (kolege) ako želite:
         * - $skipIfExists = true -> neće dirati recepte koji već postoje
         * - $skipIfExists = false -> možeš npr. update-at ili attach dodatne sastojke (ovdje ostavljamo true)
         */
        $skipIfExists = true;

        /**
         * NOTE:
         * Pretpostavka: Recipe model ima relaciju ingredients() many-to-many.
         * Ako se kod vas pivot zove drugačije ili relationship nije podešen, treba prilagodba.
         */
        $croatianRecipes = [
            [
                'name' => 'Rižoto od piletine i povrća',
                'instructions' => "1. Narežite piletinu na kockice i popržite na tavi.\n2. Dodajte luk i mrkvu, pirjajte.\n3. Ubacite rižu i kratko tostirajte.\n4. Podlijevajte temeljcem uz miješanje.\n5. Odmorite 5 minuta i poslužite.",
                'calories' => 450,
                'protein' => 30,
                'carbs' => 50,
                'fat' => 12,
                'prep_time' => 35,
                'ingredients' => ['Piletina', 'Riža', 'Luk', 'Mrkva', 'Pileći temeljac', 'Maslinovo ulje'],
            ],
            [
                'name' => 'Zobena kaša s bobičastim voćem',
                'instructions' => "1. Pomiješajte zob i mlijeko.\n2. Kuhajte 5-7 min.\n3. Dodajte med.\n4. Dodajte bobičasto voće.\n5. Poslužite toplo.",
                'calories' => 320,
                'protein' => 12,
                'carbs' => 55,
                'fat' => 6,
                'prep_time' => 10,
                'ingredients' => ['Zobene pahuljice', 'Mlijeko', 'Med', 'Bobičasto voće'],
            ],
            [
                'name' => 'Tost s avokadom i jajetom',
                'instructions' => "1. Tostirajte kruh.\n2. Zgnječite avokado, sol/papar.\n3. Ispecite jaje.\n4. Složite na tost.",
                'calories' => 380,
                'protein' => 16,
                'carbs' => 28,
                'fat' => 22,
                'prep_time' => 10,
                'ingredients' => ['Integralni kruh', 'Avokado', 'Jaje', 'Sol', 'Papar'],
            ],
            [
                'name' => 'Tjestenina s tunom i rajčicom',
                'instructions' => "1. Skuhajte tjesteninu.\n2. Na ulju kratko popržite češnjak.\n3. Dodajte rajčicu i tunu.\n4. Pomiješajte s tjesteninom.\n5. Pospite peršinom.",
                'calories' => 420,
                'protein' => 25,
                'carbs' => 60,
                'fat' => 10,
                'prep_time' => 15,
                'ingredients' => ['Tjestenina', 'Tuna', 'Rajčica', 'Češnjak', 'Maslinovo ulje', 'Peršin'],
            ],
            [
                'name' => 'Varivo od leće',
                'instructions' => "1. Pirjajte luk, mrkvu i celer.\n2. Dodajte leću i temeljac.\n3. Kuhajte ~30 min.\n4. Dodajte pasiranu rajčicu i začine.\n5. Poslužite.",
                'calories' => 280,
                'protein' => 18,
                'carbs' => 45,
                'fat' => 5,
                'prep_time' => 40,
                'ingredients' => ['Leća', 'Luk', 'Mrkva', 'Celer', 'Pasirana rajčica', 'Povrtni temeljac'],
            ],
            [
                'name' => 'Omlet sa špinatom i sirom',
                'instructions' => "1. Umutite jaja s malo mlijeka.\n2. Povenite špinat.\n3. Prelijte jajima.\n4. Dodajte sir i preklopite.\n5. Pecite još minutu.",
                'calories' => 300,
                'protein' => 20,
                'carbs' => 5,
                'fat' => 22,
                'prep_time' => 10,
                'ingredients' => ['Jaja', 'Špinat', 'Sir', 'Mlijeko', 'Ulje'],
            ],

            // ✅ DODATNI (da kolege imaju više primjera)

            [
                'name' => 'Piletina s rižom i brokulom (meal-prep)',
                'instructions' => "1. Skuhajte rižu.\n2. Ispecite piletinu na tavi.\n3. Brokulu kratko obarite.\n4. Sve začinite maslinovim uljem, solju i limunom.\n5. Spremno za meal-prep.",
                'calories' => 520,
                'protein' => 40,
                'carbs' => 55,
                'fat' => 14,
                'prep_time' => 25,
                'ingredients' => ['Piletina', 'Riža', 'Brokula', 'Maslinovo ulje', 'Limun', 'Sol'],
            ],
            [
                'name' => 'Grčki jogurt s medom i orasima',
                'instructions' => "1. U zdjelu stavite jogurt.\n2. Dodajte med.\n3. Pospite orasima.\n4. Po želji dodajte cimet.",
                'calories' => 260,
                'protein' => 18,
                'carbs' => 20,
                'fat' => 12,
                'prep_time' => 3,
                'ingredients' => ['Grčki jogurt', 'Med', 'Orasi', 'Cimet'],
            ],
            [
                'name' => 'Salata od tune i graha',
                'instructions' => "1. Ocijedite tunu i grah.\n2. Dodajte luk i kukuruz.\n3. Začinite maslinovim uljem i limunom.\n4. Promiješajte i poslužite.",
                'calories' => 390,
                'protein' => 30,
                'carbs' => 25,
                'fat' => 15,
                'prep_time' => 8,
                'ingredients' => ['Tuna', 'Grah', 'Luk', 'Kukuruz', 'Maslinovo ulje', 'Limun'],
            ],
            [
                'name' => 'Povrtna juha',
                'instructions' => "1. Narežite povrće.\n2. Kuhajte u vodi/temeljcu 25 min.\n3. Začinite po želji.\n4. Poslužite toplo.",
                'calories' => 180,
                'protein' => 6,
                'carbs' => 30,
                'fat' => 4,
                'prep_time' => 30,
                'ingredients' => ['Mrkva', 'Celer', 'Krumpir', 'Luk', 'Povrtni temeljac', 'Začini'],
            ],
            [
                'name' => 'Puretina u umaku od rajčice',
                'instructions' => "1. Popržite luk.\n2. Dodajte mljevenu puretinu.\n3. Dodajte pasiranu rajčicu.\n4. Kuhajte 15 min.\n5. Poslužite uz tjesteninu ili rižu.",
                'calories' => 430,
                'protein' => 35,
                'carbs' => 25,
                'fat' => 18,
                'prep_time' => 25,
                'ingredients' => ['Puretina', 'Luk', 'Pasirana rajčica', 'Češnjak', 'Maslinovo ulje', 'Začini'],
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($croatianRecipes as $recipeData) {
            $ingredientsList = $recipeData['ingredients'];
            unset($recipeData['ingredients']);

            $recipeData['user_id'] = $user->id;

            if ($skipIfExists && Recipe::where('user_id', $user->id)->where('name', $recipeData['name'])->exists()) {
                $skipped++;
                continue;
            }

            // create recipe
            $recipe = Recipe::create($recipeData);

            // attach ingredients (firstOrCreate => ne duplira ingredient name)
            foreach ($ingredientsList as $ingredientName) {
                $ingredient = Ingredient::firstOrCreate(['name' => $ingredientName]);

                /**
                 * ✅ Važno:
                 * syncWithoutDetaching -> ne duplira pivot red
                 * (bolje od attach ako se seeder pokrene više puta)
                 */
                $recipe->ingredients()->syncWithoutDetaching([$ingredient->id]);
            }

            $created++;
        }

        $this->command->info("✅ Croatian recipes seed complete. Created: {$created}, Skipped(existing): {$skipped}.");
        $this->command->info("ℹ️ Seeded for user: {$user->email} (id: {$user->id}).");
    }
}