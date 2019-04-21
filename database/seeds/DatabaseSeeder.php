<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables([
            'users'
        ]);

        $this->call(UsersSeeder::class);
        $this->call(ContratosSeeder::class);
        $this->call(TagsSeeder::class);
        $this->call(NumeroEquiposSeeder::class);
        $this->call(CamposSeeder::class);
        $this->call(AreasSeeder::class);
        $this->call(CargosSeeder::class);
        $this->call(EmpleadosSeeder::class);
        $this->call(PlantasSeeder::class);
        $this->call(SistemasSeeder::class);
        $this->call(UbicacionTecnicasSeeder::class);
        $this->call(ValoracionRamsSeeder::class);
        $this->call(CentroCostosSeeder::class);
        $this->call(EquiposSeeder::class);
        $this->call(TipoEventosSeeder::class);
        $this->call(TipoMantenimientosSeeder::class);
    }

    protected function truncateTables (array $tables){
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($tables as $table){
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
