<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('job_recommendations', function (Blueprint $table) {
        $table->foreignId('cv_id')->nullable()->constrained('cvs')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_recommendations', function (Blueprint $table) {
            //
        });
    }
};
