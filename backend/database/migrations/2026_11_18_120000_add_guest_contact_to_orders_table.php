<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Pembelian tamu: user_id boleh kosong, dan setiap order menyimpan email kontak untuk lacak pesanan.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('contact_email')->nullable()->after('user_id');
            $table->string('contact_whatsapp', 20)->nullable()->after('contact_email');
        });

        // Order lama milik akun: pakai email akunnya.
        DB::table('orders')->whereNull('contact_email')->update([
            'contact_email' => DB::raw('(select email from users where users.id = orders.user_id)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['contact_email', 'contact_whatsapp']);
        });
    }
};
