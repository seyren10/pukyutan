<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cycles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("round_number");
            $table->unsignedInteger("cycle_number");
            $table->date("due_date");

            $table->foreignId("group_id")->constrained()->cascadeOnDelete();
            $table->foreignId("recipient_member_id")->constrained("members");
            
            $table->timestamp("disbursed_at")->nullable();
            $table->decimal("disbursed_amount")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycles');
    }
};
