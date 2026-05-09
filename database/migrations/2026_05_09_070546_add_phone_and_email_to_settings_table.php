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
    Schema::table('settings', function (Blueprint $table) {

        // 📞 COMPANY PHONE NUMBER
        $table->string('phone_number')->nullable();

        // 📧 COMPANY EMAIL
        $table->string('email')->nullable();

    });
}

public function down()
{
    Schema::table('settings', function (Blueprint $table) {

        $table->dropColumn([
            'phone_number',
            'email'
        ]);

    });
}

};
