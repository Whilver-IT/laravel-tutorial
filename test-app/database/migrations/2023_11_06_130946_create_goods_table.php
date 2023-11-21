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
        Schema::create('goods', function (Blueprint $table) {
            $table->string('id', 8);
            $table->text('name');
            $table->timestampTz('created_at', 6);
            $table->timestampTz('updated_at', 6);
            // 以下の記述でもよい
            // timestampsTz(6)でcreated_at、updated_atをタイムゾーン付きで作成
            //$table->timestampsTz(6);

            // プライマリキー
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};
