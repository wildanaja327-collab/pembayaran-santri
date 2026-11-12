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
        if (Schema::hasTable('tagihan') && !Schema::hasColumn('tagihan', 'kategori_tunggakan')) {
            Schema::table('tagihan', function (Blueprint $table) {
                $table->string('kategori_tunggakan', 50)->nullable()->after('status_tagihan');
                $table->string('alasan_tunggakan', 255)->nullable()->after('kategori_tunggakan');
                $table->date('tgl_janji_bayar')->nullable()->after('alasan_tunggakan');
                $table->text('catatan_bendahara')->nullable()->after('tgl_janji_bayar');
                $table->dateTime('updated_alasan_at')->nullable()->after('catatan_bendahara');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tagihan') && Schema::hasColumn('tagihan', 'kategori_tunggakan')) {
            Schema::table('tagihan', function (Blueprint $table) {
                $table->dropColumn([
                    'kategori_tunggakan',
                    'alasan_tunggakan',
                    'tgl_janji_bayar',
                    'catatan_bendahara',
                    'updated_alasan_at',
                ]);
            });
        }
    }
};

