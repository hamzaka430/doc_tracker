<?php
$user = \App\Models\User::first() ?? \App\Models\User::factory()->create(['password' => bcrypt('password')]);
$stages = \App\Models\Product::getStages();
$types = \App\Models\Product::getTypes();
for($i=1; $i<=10; $i++) {
    \App\Models\Product::create([
        'user_id' => $user->id,
        'name' => 'Dummy Document ' . rand(100, 999),
        'batch_no' => 'BCH-200' . $i,
        'stage' => $stages[array_rand($stages)],
        'type' => $types[array_rand($types)],
        'status' => 'pending',
        'line_clearance' => false,
        'review' => false,
        'confirmation' => false,
        'remarks' => 'Auto generated dummy document ' . $i,
        'submission_date' => null,
        'submission_time' => null
    ]);
}
echo "10 dummy documents created successfully for User: " . $user->email . "\n";
