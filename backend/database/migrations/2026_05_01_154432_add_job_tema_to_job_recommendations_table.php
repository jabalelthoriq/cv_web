<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('job_recommendations', function (Blueprint $table) {
            $table->string('job_tema')->nullable()->after('job_link');
        });
    }

    public function down()
    {
        Schema::table('job_recommendations', function (Blueprint $table) {
            $table->dropColumn('job_tema');
        });
    }
};