<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom pencatatan konfirmasi wali murid (Task 6/7).
     */
    public function up(): void
    {
        Schema::table('izin', function (Blueprint $table) {
            $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dikonfirmasi_pada')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dikonfirmasi_oleh');
            $table->dropColumn('dikonfirmasi_pada');
        });
    }
};
