<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * El logotipo del cliente pasa a ser opcional:
 * un cliente puede mostrarse solo con su nombre.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('logo')->nullable()->change();
        });

        DB::table('clients')->where('logo', '')->update(['logo' => null]);
    }

    public function down(): void
    {
        DB::table('clients')->whereNull('logo')->update(['logo' => '']);

        Schema::table('clients', function (Blueprint $table) {
            $table->string('logo')->nullable(false)->change();
        });
    }
};
