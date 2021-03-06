<?php
/**
 * SettingsMigration.php
 *
 * PHP version 7
 *
 * @category    Migrations
 * @package     Xpressengine\Migrations
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Migrations;

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use Xpressengine\Support\Migration;

/**
 * Class SettingsMigration
 *
 * @category    Migrations
 * @package     Xpressengine\Migrations
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2019 Copyright XEHub Corp. <https://www.xehub.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        https://xpressengine.io
 */
class SettingsMigration extends Migration
{
    /**
     * Run after installation.
     *
     * @return void
     */
    public function installed()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        \DB::table('config')->insert(['name' => 'settings', 'vars' => '[]']);
        \DB::table('permissions')->insert([
            'site_key'=> 'default', 'name' => 'settings', 'grants' => '[]',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        \DB::table('permissions')->insert([
            'site_key'=> 'default', 'name' => 'settings.user', 'grants' => '[]',
            'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    /**
     * Run when install the application.
     *
     * @return void
     */
    public function install()
    {
        Schema::create('admin_log', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->string('id', 36)->comment('Log ID');
            $table->string('type', 255)->comment('logger type');
            $table->string('user_id', 36)->comment('user id');
            $table->char('method', 10)->comment('http method of request');
            $table->string('url', 1000)->comment('url of request');
            $table->text('parameters')->comment('parameters of request');
            $table->string('summary', 2000)->comment('summary for request');
            $table->text('data')->comment('extra data for request');
            $table->string('ipaddress', 16)->comment('ip address');
            $table->timestamp('created_at')->nullable()->index()->comment('created date');
            $table->timestamp('updated_at')->nullable()->index()->comment('updated date');

            $table->primary('id');
        });
    }
}
