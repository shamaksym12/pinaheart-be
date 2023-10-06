<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddViewsForUserMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE OR REPLACE
        VIEW `view_user_sent_message` AS select
            `d`.`id` AS `dialog_id`,
            `m`.`id` AS `message_id`,
            if(`m`.`is_from`,
            `x`.`id`,
            `y`.`id`) AS `user_id`
        from
            (((`dialogs` `d`
        join `messages` `m` on
            ((`d`.`id` = `m`.`dialog_id`)))
        join `users` `x` on
            ((`d`.`from` = `x`.`id`)))
        join `users` `y` on
            ((`d`.`to` = `y`.`id`)));';
        DB::statement($sql);
        $sql = 'CREATE OR REPLACE
        VIEW `view_user_inbox_message` AS select
            `d`.`id` AS `dialog_id`,
            `m`.`id` AS `message_id`,
            if( ! `m`.`is_from`,
            `x`.`id`,
            `y`.`id`) AS `user_id`
        from
            (((`dialogs` `d`
        join `messages` `m` on
            ((`d`.`id` = `m`.`dialog_id`)))
        join `users` `x` on
            ((`d`.`from` = `x`.`id`)))
        join `users` `y` on
            ((`d`.`to` = `y`.`id`)));';
        DB::statement($sql);
        $sql = 'CREATE OR REPLACE
        VIEW `view_user_unread_inbox_message` AS select
            `d`.`id` AS `dialog_id`,
            `m`.`id` AS `message_id`,
            if((not(`m`.`is_from`)),
            `x`.`id`,
            `y`.`id`) AS `user_id`
        from
            (((`dialogs` `d`
        join `messages` `m` on
            (((`d`.`id` = `m`.`dialog_id`)
            and isnull(`m`.`read_at`))))
        join `users` `x` on
            ((`d`.`from` = `x`.`id`)))
        join `users` `y` on
            ((`d`.`to` = `y`.`id`)))';
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW `view_user_sent_message`;');
        DB::statement('DROP VIEW `view_user_inbox_message`;');
        DB::statement('DROP VIEW `view_user_unread_inbox_message`;');
    }
}
