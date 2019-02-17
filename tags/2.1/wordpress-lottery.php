<?php
/*
Plugin Name: WordPress Lottery
Plugin URI: https://github.com/czp3009/wordpress-lottery
Description: WordPress 抽奖插件
Version: 2.1
Author: czp3009
Author URI: https://www.hiczp.com
License: GPL2
*/

/*  Copyright 2017  czp3009  (email : czp3009@gmail.com)

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
define('WORDPRESS_LOTTERY_DIR_PATH', plugin_dir_path(__FILE__));
define('WORDPRESS_LOTTERY_DIR_URL', plugin_dir_url(__FILE__));

//设置页面
include WORDPRESS_LOTTERY_DIR_PATH . 'options.php';
/** @noinspection PhpUndefinedClassInspection */
add_action('admin_menu', array(new WordPressLotterySettingPage(), 'createMenu'));

/** @noinspection PhpUndefinedClassInspection */

class WordPressLottery
{
    const REPLACED_TEXT = '[wordpress_lottery]';

    const SCRIPT_HANDLE = 'wordpress-lottery-script';
    const SCRIPT_FILE_URL = WORDPRESS_LOTTERY_DIR_URL . 'common/js/wordpress-lottery.js';
    const STYLE_HANDLE = 'wordpress-lottery-style';
    const STYLE_FILE_URL = WORDPRESS_LOTTERY_DIR_URL . 'common/css/wordpress-lottery.css';

    public function contentFilter($content)
    {
        $replaceText = '';
        //在首页中不起效
        if (!is_home()) {
            $replaceText =
                "<div class='wordpress-lottery-container'>" .
                "<input class='wordpress-lottery-input " . get_option('wordpress_lottery_custom_input_class', '') . "' type='text' placeholder='获奖名额' style='" . get_option('wordpress_lottery_custom_input_style', '') . "'/>" .
                "<button class='wordpress-lottery-button " . get_option('wordpress_lottery_custom_button_class', '') . "' type='button'  style='" . get_option('wordpress_lottery_custom_button_style', '') . "'>欧洲人检测</button>" .
                "<div class='wordpress-lottery-loader' hidden></div>" .
                "<div class='wordpress-lottery-canvas'></div>" .
                "</div>";
        }
        return str_replace(
            self::REPLACED_TEXT,
            $replaceText,
            $content
        );
    }

    public function load()
    {
        //仅在文章页面加载
        if (is_single()) {
            //加载 js
            wp_register_script(self::SCRIPT_HANDLE, self::SCRIPT_FILE_URL, array(), false, true);
            wp_enqueue_script(self::SCRIPT_HANDLE);

            //加载 css
            wp_register_style(self::STYLE_HANDLE, self::STYLE_FILE_URL);
            wp_enqueue_style(self::STYLE_HANDLE);

            //Viewer-Facing Side 需要自己传入 ajaxUrl 值
            wp_localize_script(self::SCRIPT_HANDLE,
                'wordpressLotteryViewData',
                array(
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'postId' => get_the_ID()
                )
            );
        }
    }

    //处理 ajax, 返回获奖者
    public function doLottery()
    {
        if (get_option('wordpress_lottery_admin_required')) {
            if (!current_user_can('manage_options')) {
                wp_send_json_error(array(
                    'message' => '仅管理员可用'
                ));
            }
        }

        $postId = intval($_POST['postId']);
        $winnerCount = intval($_POST['winnerCount']);

        //参数非法
        if ($postId <= 0 || $winnerCount <= 0) {
            wp_send_json_error(array(
                'message' => '参数非法'
            ));
        }

        //获奖人数超过了楼层数
        global $wpdb;
        /** @noinspection SqlDialectInspection */
        /** @noinspection SqlNoDataSourceInspection */
        $commentCount = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE `comment_post_ID` = $postId AND `comment_parent` = 0 AND `comment_approved` = 1");
        if ($winnerCount > $commentCount) {
            wp_send_json_error(array(
                'message' => '没有这么多楼层'
            ));
        }

        //取出回复贴, 随机排序取首部 N 个
        /** @noinspection SqlDialectInspection */
        /** @noinspection SqlNoDataSourceInspection */
        $winners = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE `comment_post_ID` = $postId AND `comment_parent` = 0 AND `comment_approved` = 1");
        shuffle($winners);
        $winners = array_slice($winners, 0, $winnerCount);

        //构造 json
        $data = array();
        foreach ($winners as $winner) {
            $data[] = array(
                'commentId' => $winner->comment_ID,
                'commentAuthor' => $winner->comment_author,
                'commentAuthorEmail' => $winner->comment_author_email
            );
        }
        wp_send_json_success($data);
    }
}

$wordpressLottery = new WordPressLottery();
add_filter('the_content', array($wordpressLottery, 'contentFilter'));
add_action('wp_enqueue_scripts', array($wordpressLottery, 'load'));

add_action('wp_ajax_wordpress_lottery_doLottery', array($wordpressLottery, 'doLottery'));
if (!get_option('wordpress_lottery_login_required')) {
    /** @noinspection SpellCheckingInspection */
    add_action('wp_ajax_nopriv_wordpress_lottery_doLottery', array($wordpressLottery, 'doLottery'));
}
