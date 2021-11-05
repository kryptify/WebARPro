<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Plan;
use App\Models\PlanBenefit;
use App\Models\Ability;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // user model
        User::create([
            'username' => 'admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin'),
            'role_id' => 3,
        ]);
        User::create([
            'username' => 'free',
            'email' => 'free@test.com',
            'password' => Hash::make('secret'),
            'role_id' => 1,
        ]);
        User::create([
            'username' => 'plus',
            'email' => 'plus@test.com',
            'password' => Hash::make('secret'),
            'role_id' => 1,
        ]);
        User::create([
            'username' => 'premium',
            'email' => 'premium@test.com',
            'password' => Hash::make('secret'),
            'role_id' => 2,
         ]);

        //role model
        Role::create([
            'id' => 1,
            'title' => 'user',
        ]);
        Role::create([
            'id' => 2,
            'title' => 'admin',
        ]);
        Role::create([
            'id' => 3,
            'title' => 'superadmin',
        ]);

        // Plan model
        Plan::create([
            'id' => 1,
            'title' => 'free',
            'subtitle' => 'For non-commercial projects and platform evaluation',
            'stripe_id' => '',
            'payment_plan' => false,
            'popular_plan' => false,
            'price' => 0,
            'interval' => '',
            'upload_size_limit' => 50, //300MB
            'store_size_limit' => 300, //300MB
        ]);
        Plan::create([
            'id' => 2,
            'title' => 'plus',
            'subtitle' => 'For startups, growing businesses and small agencies',
            'stripe_id' => 'price_1JqOEGENiSwMUFhh3QyvJGse',
            'payment_plan' => true,
            'popular_plan' => false,
            'price' => 35,
            'interval' => 'monthly',
            'upload_size_limit' => 100, //100MB
            'store_size_limit' => 500, //500MB
        ]);
        Plan::create([
            'id' => 3,
            'title' => 'premium',
            'subtitle' => 'Solution for big organizations and advanced AI features',
            'stripe_id' => 'price_1JqOFkENiSwMUFhh1Q3escUK',
            'payment_plan' => true,
            'popular_plan' => true,
            'price' => 249,
            'interval' => 'monthly',
            'upload_size_limit' => 300, // 300MB
            'store_size_limit' => 2000, // 2GB
        ]);
        Plan::create([
            'id' => 4,
            'title' => 'plus',
            'subtitle' => 'For startups, growing businesses and small agencies',
            'stripe_id' => 'price_1JqOGPENiSwMUFhhhDTdLeMt',
            'payment_plan' => true,
            'popular_plan' => false,
            'price' => 350,
            'interval' => 'yearly',
            'upload_size_limit' => 100, //100MB
            'store_size_limit' => 500, //500MB
        ]);
        Plan::create([
            'id' => 5,
            'title' => 'premium',
            'subtitle' => 'Solution for big organizations and advanced AI features',
            'stripe_id' => 'price_1JqOGqENiSwMUFhhDOnVURcP',
            'payment_plan' => true,
            'popular_plan' => true,
            'price' => 2490,
            'interval' => 'yearly',
            'upload_size_limit' => 300, // 300MB
            'store_size_limit' => 2000, // 2GB
        ]);

        // Plan benefit model
        PlanBenefit::create([
            'id' => 1,
            'desc' => 'Easy-to-Use Editor',
            'plan' => 'free',
        ]);
        PlanBenefit::create([
            'id' => 2,
            'desc' => 'Premium Projects',
            'plan' => 'plus',
        ]);
        PlanBenefit::create([
            'id' => 3,
            'desc' => 'Enterprise',
            'plan' => 'premium',
        ]);
        PlanBenefit::create([
            'id' => 4,
            'desc' => 'WebAR viewer web embeds & AR',
            'plan' => 'free',
        ]);
        PlanBenefit::create([
            'id' => 5,
            'desc' => 'Photorealistic rendering',
            'plan' => 'plus',
        ]);
        PlanBenefit::create([
            'id' => 6,
            'desc' => '3D Photorealistic rendering',
            'plan' => 'premium',
        ]);
    }
}
