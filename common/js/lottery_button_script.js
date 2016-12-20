/**
 * Created by czp on 16-12-19.
 */

//defined
var ajaxurl = defined.ajaxurl;
var wordpress_lottery_dir_url = defined.wordpress_lottery_dir_url;
var post_id = defined.post_id;

var user_count_input = jQuery("#user_count");
var lottery_button = jQuery(".lottery_button");
var canvas_div = jQuery("#canvas");
var can_modify = false;

lottery_button.prop("disabled", true);

user_count_input.keyup(function () {
    //获奖人数 为空或为0或不为数字时, 禁止提交
    if (user_count_input.val() == "") {
        lottery_button.prop("disabled", true);
        if (can_modify) {
            canvas_div.html("");
        }
    } else if (parseInt(user_count_input.val()) == 0 || isNaN(parseInt(user_count_input.val()))) {
        lottery_button.prop("disabled", true);
        canvas_div.html("参数非法");
        can_modify = true;
    } else {
        lottery_button.prop("disabled", false);
        if (can_modify) {
            canvas_div.html("");
        }
    }
});

lottery_button.click(function () {
    canvas_div.html(
        "<img src='" + wordpress_lottery_dir_url + "common/img/spinner-rosetta-gray-14x14.gif" + "' style='margin-right: 10px'/>" +
        "加载中..."
    );

    jQuery.ajax({
        url: ajaxurl,
        method: "POST",
        dataType: "json",
        data: {
            action: "wordpress_lottery_doLottery",
            post_id: post_id,
            user_count: user_count_input.val()
        },
        success: function (data) {
            canvas_div.html("");

            //未登录
            if (data == 0) {
                canvas_div.html("登陆后才能检测血统");
                can_modify = true;
                return;
            }

            if (data.code != 0) {
                canvas_div.html(data.result);
                can_modify = true;
            } else {
                data.result.forEach(function (winner) {
                    canvas_div.append(
                        "<p>" +
                        "评论ID: '" + winner.comment_ID + "' " +
                        "用户名: '" + winner.comment_author + "' " +
                        "电子邮件: '" + winner.comment_author_email + "'" +
                        "</p>"
                    );
                });
                can_modify = false;
            }
        }
    });
});
