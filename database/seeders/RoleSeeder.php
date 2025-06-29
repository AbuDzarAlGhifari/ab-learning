<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder {
    public function run() {
        collect(['admin','finance','teacher','student'])->each(fn($name) =>
            Role::firstOrCreate(['name'=>$name])
        );
    }
}
