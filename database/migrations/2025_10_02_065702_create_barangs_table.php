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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('jenis_barang')->nullable();
            $table->text('keterangan')->nullable();

            // stok terkini
            $table->integer('stok_baik')->default(0);
            $table->integer('stok_rusak')->default(0);

            // harga default ambil dari transaksi terakhir
            $table->decimal('harga_default', 15, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
