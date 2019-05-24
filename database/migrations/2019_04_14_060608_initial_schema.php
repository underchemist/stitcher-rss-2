<?php declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitialSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeds', function (Blueprint $table) {
            $table->unsignedInteger('id')->unique();
            $table->string('title')->nullable(true)->default(null);
            $table->text('description')->nullable(true)->default(null);
            $table->string('image_url')->nullable(true)->default(null);

            // Fields used for determining whether to render, refresh, etc.
            $table->boolean('is_premium')->unsigned()->default(true)->index();
            $table->timestamp('last_refresh')->nullable()->default(null)->index();

            $table->timestamps();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->unsignedInteger('id')->unique();
            $table->unsignedInteger('feed_id')->index();
            $table->string('title')->nullable(true)->default(null);
            $table->text('description')->nullable(true)->default(null);
            $table->string('pub_date')->nullable(true)->default(null);
            $table->string('itunes_duration')->nullable(true)->default(null);
            $table->integer('itunes_season')->nullable(true)->default(null);
            $table->integer('itunes_episode')->nullable(true)->default(null);
            $table->text('enclosure_url')->nullable(true)->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('episodes');
        Schema::dropIfExists('shows');
    }
}
