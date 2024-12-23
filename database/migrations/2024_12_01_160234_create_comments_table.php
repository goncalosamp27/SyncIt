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
        Schema::create('comment', function (Blueprint $table) {
            $table->id('comment_id'); // Use 'comment_id' as the primary key
            $table->text('text');
            $table->string('file_path')->nullable(); // Add the 'file_path' column
            $table->timestamp('comment_date')->default(DB::raw('CURRENT_TIMESTAMP'))->check('comment_date >= CURRENT_DATE');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('member_id')->on('member')->onDelete('cascade');
            $table->foreign('event_id')->references('event_id')->on('event')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('comment_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('comment')->onDelete('cascade');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('member_id')->on('member')->onDelete('cascade');
            $table->enum('vote', ['up', 'down']);
            $table->timestamps();
        });

        // Create indexes
        Schema::table('comment', function (Blueprint $table) {
            $table->index('event_id', 'comment_event_id_idx');
            $table->index('member_id', 'comment_member_id_idx');
            $table->index('comment_date', 'comment_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_votes');
        Schema::dropIfExists('comment');
    }
};