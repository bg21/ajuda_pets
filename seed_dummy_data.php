<?php
$user = App\Models\User::find(2);
if (!$user) { $user = App\Models\User::first(); }

if ($user) {
    // Delete existing dummy data to avoid duplicates if run multiple times
    $user->pets()->each(function($pet) {
        $pet->weights()->delete();
        $pet->vaccines()->delete();
        $pet->delete();
    });

    $pet1 = App\Models\Pet::create([
        'user_id' => $user->id,
        'uuid' => \Illuminate\Support\Str::uuid(),
        'name' => 'Max',
        'species' => 'Cachorro',
        'breed' => 'Golden Retriever',
        'gender' => 'Macho',
        'birth_date' => now()->subYears(3)->format('Y-m-d'),
        'coat_color' => 'Dourado'
    ]);

    $pet2 = App\Models\Pet::create([
        'user_id' => $user->id,
        'uuid' => \Illuminate\Support\Str::uuid(),
        'name' => 'Luna',
        'species' => 'Gato',
        'breed' => 'Siamês',
        'gender' => 'Fêmea',
        'birth_date' => now()->subYears(1)->subMonths(5)->format('Y-m-d'),
        'coat_color' => 'Branco e Marrom'
    ]);

    App\Models\Weight::create(['pet_id' => $pet1->id, 'weight' => 28.5, 'recorded_at' => now()->subMonths(3)]);
    App\Models\Weight::create(['pet_id' => $pet1->id, 'weight' => 29.1, 'recorded_at' => now()->subMonths(2)]);
    App\Models\Weight::create(['pet_id' => $pet1->id, 'weight' => 29.5, 'recorded_at' => now()->subMonths(1)]);
    App\Models\Weight::create(['pet_id' => $pet1->id, 'weight' => 29.2, 'recorded_at' => now()]);

    App\Models\Weight::create(['pet_id' => $pet2->id, 'weight' => 3.5, 'recorded_at' => now()->subMonths(2)]);
    App\Models\Weight::create(['pet_id' => $pet2->id, 'weight' => 3.8, 'recorded_at' => now()->subMonths(1)]);
    App\Models\Weight::create(['pet_id' => $pet2->id, 'weight' => 4.0, 'recorded_at' => now()]);

    App\Models\Vaccine::create([
        'pet_id' => $pet1->id,
        'name' => 'V10 (Múltipla)',
        'date_given' => now()->subMonths(11),
        'next_due_date' => now()->addDays(15),
        'batch_number' => 'Lote-9928A'
    ]);
    App\Models\Vaccine::create([
        'pet_id' => $pet1->id,
        'name' => 'Antirrábica',
        'date_given' => now()->subYears(1),
        'next_due_date' => now()->addDays(3),
        'batch_number' => 'Lote-Rab-112'
    ]);
    App\Models\Vaccine::create([
        'pet_id' => $pet1->id,
        'name' => 'Gripe Canina',
        'date_given' => now()->subMonths(6),
        'next_due_date' => now()->addMonths(6),
        'batch_number' => 'Lote-GC-444'
    ]);

    App\Models\Vaccine::create([
        'pet_id' => $pet2->id,
        'name' => 'V4 (Quádrupla Felina)',
        'date_given' => now()->subMonths(10),
        'next_due_date' => now()->addDays(45),
        'batch_number' => 'Lote-F-90'
    ]);
    App\Models\Vaccine::create([
        'pet_id' => $pet2->id,
        'name' => 'Antirrábica',
        'date_given' => now()->subYears(2),
        'next_due_date' => null,
        'batch_number' => 'Lote-Rab-001'
    ]);
    
    echo "Dummy data created successfully for user: " . $user->name;
} else {
    echo "No users found in the database.";
}
