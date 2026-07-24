<?php

use App\Enums\GroupShareStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_shares', function (Blueprint $table) {
            $table->id();
            $table->string("status")->default(GroupShareStatus::PENDING);
            $table->timestamp("requested_at");
            $table->timestamp("responded_at")->nullable();

            $table->foreignId("group_id")->constrained()->cascadeOnDelete();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();

            $table->unique(["group_id", "user_id"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_shares');
    }
};
