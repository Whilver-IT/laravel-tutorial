<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->text('explanation')->nullable();
            // 以下のように書いても残念ながらPostgreSQLでは不可能(カラム自体は末尾に追加される)
            //$table->after('name', function (BluePrint $table) {
            //    $table->text('explanation')->nullable();
            //});
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            // rollbackした際に元の状態に戻るように書く
            $table->dropColumn('explanation');
        });
    }
};
