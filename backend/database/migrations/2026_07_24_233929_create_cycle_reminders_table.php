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
        Schema::create('cycle_reminders', function (Blueprint $table) {
            $table->id();
            $table->timestamp("sent_at");

            $table->foreignId("cycle_id")->constrained()->cascadeOnDelete();
            $table->foreignId("member_id")->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(["member_id","cycle_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycle_reminders');
    }
};
