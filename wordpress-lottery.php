<?php
/**
 * Created by PhpStorm.
 * User: czp
 * Date: 16-12-19
 * Time: 下午9:47
 */

/*
Plugin Name: WordPress Lottery
Plugin URI: 暂无
Description: 欧洲人检测器
Version: 0.2
Author: czp
Author URI: https://www.hiczp.com
License: GPL2
*/

/*  Copyright 2016  czp3009  (email : czp3009@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//预定义变量
define("WORDPRESS_LOTTERY_DIR_PATH", plugin_dir_path(__FILE__));
define("WORDPRESS_LOTTERY_DIR_URL", plugin_dir_url(__FILE__));

class wordPressLottery
{
    public function load()
    {
        //仅在文章页面加载
        if (is_single()) {
            //加载 js
            wp_register_script(
                "lottery_button_script",
                WORDPRESS_LOTTERY_DIR_URL . "common/js/lottery_button_script.js",
                array("jquery"),
                "",
                true
            );
            wp_enqueue_script("lottery_button_script");

            //加载 css
            wp_register_style("lottery_button_style", WORDPRESS_LOTTERY_DIR_URL . "common/css/lottery_button_style.css");
            wp_enqueue_style("lottery_button_style");

            //Viewer-Facing Side 需要自己传入 ajaxurl 值
            wp_localize_script("lottery_button_script",
                "defined",
                array(
                    "ajaxurl" => admin_url("admin-ajax.php"),
                    "wordpress_lottery_dir_url" => WORDPRESS_LOTTERY_DIR_URL,
                    "post_id" => get_the_ID()
                )
            );
        }
    }

    //处理 ajax, 返回获奖者
    public function doLottery()
    {
        ob_clean();
        global $wpdb;
        $post_id = intval($_POST["post_id"]);
        $user_count = intval($_POST["user_count"]);
        $result = array(
            "code" => -1,
            "result" => ""
        );

        if ($post_id == 0 || $user_count == 0) {
            $result["result"] = "参数非法";
            wp_die(json_encode($result));
        }

        $total_user_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE `comment_post_ID` = $post_id AND `comment_parent` = 0");
        if ($user_count > $total_user_count) {
            $result["result"] = "没有这么多楼层";
            wp_die(json_encode($result));
        }

        //取出回复贴, 随机排序取首部 N 个
        $winners = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE `comment_post_ID` = $post_id AND `comment_parent` = 0");
        shuffle($winners);
        $winners = array_slice($winners, 0, $user_count);

        //构造 json
        $data = array();
        foreach ($winners as $winner) {
            $data[] = array(
                "comment_ID" => $winner->comment_ID,
                "comment_author" => $winner->comment_author,
                "comment_author_email" => $winner->comment_author_email
            );
        }
        $result["code"] = 0;
        $result["result"] = $data;
        print json_encode($result);

        wp_die();
    }
}

$wordpress_lottery = new wordPressLottery();
add_action("wp_enqueue_scripts", array($wordpress_lottery, "load"));
add_action("wp_ajax_wordpress_lottery_doLottery", array($wordpress_lottery, "doLottery"));
