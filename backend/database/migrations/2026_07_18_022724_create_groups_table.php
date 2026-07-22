<?php

use App\Enums\FrequencyUnitType;
use App\Enums\GroupStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->decimal("contribution_amount", 10, 2);
            $table->string("frequency_unit")->default(FrequencyUnitType::WEEKLY);
            $table->unsignedInteger("frequency_interval")->default(1);
            $table->date("start_date");
            $table->string("status")->default(GroupStatus::DRAFT);
            $table->string("invite_code")->unique();

            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
