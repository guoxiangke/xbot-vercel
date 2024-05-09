<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWechatAutoRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_auto_replies', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->index()->comment('@see Str::is()');
            $table->integer("wechat_bot_id")->unsigned()->index()->comment('所属bot');// 不过内容里有 wechat_bot_id 了。
            $table->integer("wechat_content_id")->unsigned()->index()->comment('回复的内容');

            $table->softDeletes();
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
        Schema::dropIfExists('wechat_auto_replies');
    }
}
