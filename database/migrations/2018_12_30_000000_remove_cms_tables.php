<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCmsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('cms_us_states');
        Schema::dropIfExists('cms_user_role_affiliation');
        Schema::dropIfExists('cms_updates');
        Schema::dropIfExists('cms_update_types');
        Schema::dropIfExists('cms_versions');
        Schema::dropIfExists('cms_static_pages');
        Schema::dropIfExists('cms_sessions');
        Schema::dropIfExists('cms_role_permission_affiliation');        
        Schema::dropIfExists('cms_meta_settings');
        Schema::dropIfExists('cms_errors');
        Schema::dropIfExists('cms_parameter_values');
        Schema::dropIfExists('cms_configuration_parameters');
        Schema::dropIfExists('cms_censored_words');
        Schema::dropIfExists('cms_cache');
        Schema::dropIfExists('cms_banned_ip_addresses');
        Schema::dropIfExists('cms_ad_campaign_affiliation');
        Schema::dropIfExists('cms_ads');
        Schema::dropIfExists('cms_ad_campaigns');
        Schema::dropIfExists('cms_roles');
        Schema::dropIfExists('cms_permissions');
        Schema::dropIfExists('cms_parameter_data_types');
        Schema::dropIfExists('cms_modules');
        Schema::dropIfExists('cms_users');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
