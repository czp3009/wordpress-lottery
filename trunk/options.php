<?php

/** @noinspection PhpUndefinedClassInspection */

class WordPressLotterySettingPage
{
    function createMenu()
    {
        add_menu_page('Wordpress Lottery Settings', 'Wordpress Lottery Settings', 'administrator', __FILE__, array($this, 'settingsPage'));
        add_action('admin_init', array($this, 'registerSettings'));
    }

    function registerSettings()
    {
        register_setting('wordpress-lottery-settings-group', 'wordpress_lottery_login_required');
        register_setting('wordpress-lottery-settings-group', 'wordpress_lottery_admin_required');
        register_setting('wordpress-lottery-settings-group', 'wordpress_lottery_custom_input_class');
        register_setting('wordpress-lottery-settings-group', 'wordpress_lottery_custom_input_style');
        register_setting('wordpress-lottery-settings-group', 'wordpress_lottery_custom_button_class');
        register_setting('wordpress-lottery-settings-group', 'wordpress_lottery_custom_button_style');
    }

    function settingsPage()
    { ?>
        <div class="wrap">
            <h1>Wordpress Lottery</h1>

            <form method="post" action="options.php">
                <?php settings_fields('wordpress-lottery-settings-group'); ?>
                <?php do_settings_sections('wordpress-lottery-settings-group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="loginRequired">仅登陆后可用</label>
                        </th>
                        <td>
                            <input type="checkbox" name="wordpress_lottery_login_required" id="loginRequired"
                                   value="true"
                                <?php echo get_option('wordpress_lottery_login_required') ? 'checked' : '' ?>/>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="adminRequired">仅管理员可用</label>
                        </th>
                        <td>
                            <input type="checkbox" name="wordpress_lottery_admin_required" id="adminRequired"
                                   value="true"
                                <?php echo get_option('wordpress_lottery_admin_required') ? 'checked' : '' ?>/>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="inputClass">自定义 输入框 的 class</label>
                        </th>
                        <td>
                            <input type="text" name="wordpress_lottery_custom_input_class" id="inputClass"
                                   value="<?php echo esc_attr(get_option('wordpress_lottery_custom_input_class')); ?>"/>
                            <p>某些主题可能使抽奖的输入框无法正确对齐, 在此可以自定义其 class</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="inputStyle">自定义 输入框 的 style</label>
                        </th>
                        <td>
                            <textarea type="text" name="wordpress_lottery_custom_input_style"
                                      id="inputStyle"><?php echo get_option('wordpress_lottery_custom_input_style') ?></textarea>
                            <p>某些主题可能使抽奖的输入框无法正确对齐, 在此可以自定义其 style</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="buttonClass">自定义 按钮 的 class</label>
                        </th>
                        <td>
                            <input type="text" name="wordpress_lottery_custom_button_class" id="buttonClass"
                                   value="<?php echo esc_attr(get_option('wordpress_lottery_custom_button_class')); ?>"/>
                            <p>某些主题可能使抽奖的按钮无法正确对齐, 在此可以自定义其 class</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="buttonStyle">自定义 按钮 的 style</label>
                        </th>
                        <td>
                            <textarea type="text" name="wordpress_lottery_custom_button_style"
                                      id="inputStyle"><?php echo get_option('wordpress_lottery_custom_button_style') ?></textarea>
                            <p>某些主题可能使抽奖的按钮无法正确对齐, 在此可以自定义其 style</p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>

            </form>
        </div>
        <?php
    }
} ?>
