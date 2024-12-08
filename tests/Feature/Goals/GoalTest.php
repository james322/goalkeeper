<?php

use App\Models\Goal;
use App\Models\User;
use Illuminate\Support\Facades\DB;

it('goals intent is encrypted and can be decrypted', function () {
    $intent = 'learn laravel';
    User::factory()->has(Goal::factory(['intent' => $intent]))->create();
    $goal = DB::table('goals')->first();

    expect($goal->intent)->not()->toBe($intent);
    expect(Goal::first()->intent)->toBe($intent);
});
