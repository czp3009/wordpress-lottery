=== Plugin Name ===
Contributors: czp3009
Donate link: https://www.hiczp.com/
Tags: comments, spam
Requires at least: 1.0
Tested up to: 1.0
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Here is a short description of the plugin.  This should be no more than 150 characters.  No markup here.

== Description ==

用于在一篇文章的回复者(不包括回复的回复)中抽选出数人. 用于抽奖.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3.在一篇文章中至少插入如下三行
<input id="user_count" type="text" placeholder="获奖名额" />
<button class="lottery_button" type="button">欧洲人检测</button>
<div id="canvas"></div>
4.保存文章, 返回前台查看文章
5.登录 WordPress 后, 在输入框中输入获奖名额并点击按钮

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Changelog ==

= 1.0 =
加入 GitHub 链接
正式发布

= 0.3 =
增加前台数据验证

= 0.2 =
登录 WordPress 后才能抽奖

= 0.1 =
完成基础功能

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`
