<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->foreignId('transaksi_masuk_id')->nullable()->constrained('transaksi_masuks')->cascadeOnDelete();
            $table->foreignId('transaksi_keluar_id')->nullable()->constrained('transaksi_keluars')->cascadeOnDelete();

            $table->enum('status', ['baik', 'rusak','terjual'])->default('baik');
            $table->integer('jumlah');
            $table->decimal('harga_beli', 15, 2)->nullable();
            $table->decimal('harga_jual', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_barangs');
    }
};
