<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // untuk subscription
            $table->unsignedBigInteger('user_id')->nullable()->after('cv_id');

            // type: cv / subscription
            $table->string('type')->default('cv')->after('user_id');

            // plan: plus / pro
            $table->string('plan')->nullable()->after('type');

            // optional index biar cepat query
            $table->index('order_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'type', 'plan']);
        });
    }
};