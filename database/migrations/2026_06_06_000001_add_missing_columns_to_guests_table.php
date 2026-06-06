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
        Schema::table('guests', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('guests', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('event_id')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('guests', 'participation_type')) {
                $table->string('participation_type')->nullable()->after('status');
            }
            if (!Schema::hasColumn('guests', 'registration_status')) {
                $table->string('registration_status')->nullable()->after('participation_type');
            }
            if (!Schema::hasColumn('guests', 'dietary_requirements')) {
                $table->text('dietary_requirements')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('guests', 'company')) {
                $table->string('company')->nullable()->after('dietary_requirements');
            }
            if (!Schema::hasColumn('guests', 'position')) {
                $table->string('position')->nullable()->after('company');
            }
            if (!Schema::hasColumn('guests', 'checked_in')) {
                $table->boolean('checked_in')->default(false)->after('position');
            }
            if (!Schema::hasColumn('guests', 'checked_in_at')) {
                $table->dateTime('checked_in_at')->nullable()->after('checked_in');
            }
            if (!Schema::hasColumn('guests', 'confirmed_at')) {
                $table->dateTime('confirmed_at')->nullable()->after('checked_in_at');
            }
            if (!Schema::hasColumn('guests', 'qr_code')) {
                $table->string('qr_code')->unique()->nullable()->after('confirmed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $columns = ['user_id', 'participation_type', 'registration_status', 'dietary_requirements', 'company', 'position', 'checked_in', 'checked_in_at', 'confirmed_at', 'qr_code'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('guests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
