<?php
/*
Plugin Name: LitRes Widget
Plugin URI: https://wordpress.org/plugins/litres-widget/
Description: Данный плагин выводит партнерский виджет покупки книг от ЛитРес.
Version: 1.01
Author: Flector
Author URI: https://profiles.wordpress.org/flector#content-plugins
Text Domain: litres-widget
*/ 

//проверка версии плагина (запуск функции установки новых опций) begin
function litreswidget_check_version() {
    $litreswidget_options = get_option('litreswidget_options');
    if ( $litreswidget_options['version'] != '1.01' ) {
        litreswidget_set_new_options();
    }    
}
add_action('plugins_loaded', 'litreswidget_check_version');
//проверка версии плагина (запуск функции установки новых опций) end

//функция установки новых опций при обновлении плагина у пользователей begin
function litreswidget_set_new_options() { 
    $litreswidget_options = get_option('litreswidget_options');

    //если нет опции при обновлении плагина - записываем ее
    //if (!isset($litreswidget_options['new_option'])) {$litreswidget_options['new_option']='value';}
    
    //если необходимо переписать уже записанную опцию при обновлении плагина
    //$litreswidget_options['old_option'] = 'new_value';
    
    $litreswidget_options['version'] = '1.01';
    update_option('litreswidget_options', $litreswidget_options);
}
//функция установки новых опций при обновлении плагина у пользователей end

//функция установки значений по умолчанию при активации плагина begin
function litreswidget_init() {

    $litreswidget_options = array();

    $litreswidget_options['version'] = '1.01';
    $litreswidget_options['lfrom'] = '';
   
    add_option('litreswidget_options', $litreswidget_options);
}
add_action('activate_litres-widget/litres-widget.php', 'litreswidget_init');
//функция установки значений по умолчанию при активации плагина end

//функция при деактивации плагина begin
function litreswidget_on_deactivation() {
	if ( ! current_user_can('activate_plugins') ) return;
}
register_deactivation_hook( __FILE__, 'litreswidget_on_deactivation' );
//функция при деактивации плагина end

//функция при удалении плагина begin
function litreswidget_on_uninstall() {
	if ( ! current_user_can('activate_plugins') ) return;
    delete_option('litreswidget_options');
}
register_uninstall_hook( __FILE__, 'litreswidget_on_uninstall' );
//функция при удалении плагина end

//загрузка файла локализации плагина begin
function litreswidget_setup(){
    load_plugin_textdomain('litres-widget');
}
add_action('init', 'litreswidget_setup');
//загрузка файла локализации плагина end

//добавление ссылки "Настройки" на странице со списком плагинов begin
function litreswidget_actions($links) {
	return array_merge(array('settings' => '<a href="options-general.php?page=litres-widget.php">' . __('Настройки', 'litres-widget') . '</a>'), $links);
}
add_filter('plugin_action_links_' . plugin_basename( __FILE__ ),'litreswidget_actions');
//добавление ссылки "Настройки" на странице со списком плагинов end

//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина begin
function litreswidget_files_admin($hook_suffix) {
	$purl = plugins_url('', __FILE__);
    if ( $hook_suffix == 'settings_page_litres-widget' ) {
        if(!wp_script_is('jquery')) {wp_enqueue_script('jquery');}    
        wp_register_script('litreswidget-lettering', $purl . '/inc/jquery.lettering.js');  
        wp_enqueue_script('litreswidget-lettering');
        wp_register_script('litreswidget-textillate', $purl . '/inc/jquery.textillate.js');
        wp_enqueue_script('litreswidget-textillate');
        wp_register_style('litreswidget-animate', $purl . '/inc/animate.min.css');
        wp_enqueue_style('litreswidget-animate');
        wp_register_script('litreswidget-script', $purl . '/inc/litreswidget-script.js', array(), '1.01');  
        wp_enqueue_script('litreswidget-script');
        wp_register_style('litreswidget-css', $purl . '/inc/litreswidget-css.css', array(), '1.01');
        wp_enqueue_style('litreswidget-css');
        wp_register_script('litreswidget', 'https://www.litres.ru/static/widgets/buy_widget/js/widget.js', false, null, false);
        wp_enqueue_script('litreswidget');
    }
}
add_action('admin_enqueue_scripts', 'litreswidget_files_admin');
//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина end

//функция загрузки скриптов и стилей плагина на внешней стороне сайта begin
function litreswidget_files_front() {
    wp_register_script('litreswidget', 'https://www.litres.ru/static/widgets/buy_widget/js/widget.js', false, null, false);
    wp_enqueue_script('litreswidget');
}    
add_action('wp_enqueue_scripts', 'litreswidget_files_front');
//функция загрузки скриптов и стилей плагина на внешней стороне сайта end

//добавляем атрибут async скрипту litres begin
function litreswidget_add_async_attribute($tag, $handle) {
    if ( 'litreswidget' !== $handle )
        return $tag;
    return str_replace( ' src', ' async src', $tag );
}
add_filter('script_loader_tag', 'litreswidget_add_async_attribute', 10, 2);
//добавляем атрибут async скрипту litres end

//функция вывода страницы настроек плагина begin
function litreswidget_options_page() {
$purl = plugins_url('', __FILE__);

if (isset($_POST['submit'])) {
     
//проверка безопасности при сохранении настроек плагина begin       
if ( ! wp_verify_nonce( $_POST['litreswidget_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
   wp_die(__( 'Cheatin&#8217; uh?', 'litres-widget' ));
}
//проверка безопасности при сохранении настроек плагина end
        
    //проверяем и сохраняем введенные пользователем данные begin    
    $litreswidget_options = get_option('litreswidget_options');
    
    $litreswidget_options['lfrom'] = sanitize_text_field($_POST['lfrom']);
    
    update_option('litreswidget_options', $litreswidget_options);
    //проверяем и сохраняем введенные пользователем данные end
}
$litreswidget_options = get_option('litreswidget_options');
?>
<?php   if (!empty($_POST) ) :
if ( ! wp_verify_nonce( $_POST['litreswidget_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
   wp_die(__( 'Cheatin&#8217; uh?', 'litres-widget' ));
}
?>
<div id="message" class="updated fade"><p><strong><?php _e('Настройки сохранены.', 'litres-widget'); ?></strong></p></div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Настройки плагина &#171;LitRes Widget&#187;', 'litres-widget'); ?></h2>

<div class="metabox-holder" id="poststuff">
<div class="meta-box-sortables">

<div class="postbox">
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode">Вам нравится этот плагин ?</span></h3>
    <div class="inside" style="display: block;margin-right: 12px;">
        <img src="<?php echo $purl . '/img/icon_coffee.png'; ?>" title="Купить мне чашку кофе :)" style=" margin: 5px; float:left;" />
        <p>Привет, меня зовут <strong>Flector</strong>.</p>
        <p>Я потратил много времени на разработку этого плагина.<br />
		Поэтому не откажусь от небольшого пожертвования :)</p>   
        <a target="_blank" id="yadonate" href="https://money.yandex.ru/to/41001443750704/200">Подарить</a>        
      <p>Или вы можете заказать у меня услуги по WordPress, от мелких правок до создания полноценного сайта.<br />
        Быстро, качественно и дешево. Прайс-лист смотрите по адресу <a target="new" href="https://www.wpuslugi.ru/?from=litreswidget-plugin">https://www.wpuslugi.ru/</a>.</p>
        <div style="clear:both;"></div>
    </div>
</div>


<form action="" method="post">

<div class="postbox">

    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e('Настройки', 'litres-widget'); ?></span></h3>
    <div class="inside" style="display: block;">

        <table class="form-table">

            <tr>
                <th><?php _e('Партнерский ID:', 'litres-widget'); ?></th>
                <td>
                    <input type="text" size="30" style="width: 250px;" name="lfrom" value="<?php echo $litreswidget_options['lfrom']; ?>" />
                    <br /><small><?php _e('Укажите ваш партнерский ID (lfrom).', 'litres-widget'); ?> </small>
               </td>
            </tr>
            
            <tr>
                <th></th>
                <td>
                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Сохранить настройки &raquo;', 'litres-widget'); ?>" />
                </td>
            </tr> 
        </table>
    </div>
</div>

<div class="postbox" style="margin-bottom:0;">
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e('О плагине', 'litres-widget'); ?></span></h3>
	  <div class="inside" style="padding-bottom:15px;display: block;">
      
      <p><?php _e('Данный плагин с помощью шорткода <span style="color:#183691;">[litres]</span> выводит виджет покупки от ЛитРес:', 'litres-widget'); ?></p>
     
      <?php echo do_shortcode( "[litres author='Джордж Мартин' title='Игра престолов']" ); ?>
      
      <p><?php _e('Синтаксис этого шорткода: <span style="color:#183691;">[litres author=\'</span><span style="color:green;">Джордж Мартин</span><span style="color:#183691;">\' title=\'</span><span style="color:green;">Игра престолов</span><span style="color:#183691;">\']</span>', 'litres-widget'); ?><br>
      </p>
      
      <p><?php _e('В классическом визуальном редакторе вы можете использовать кнопку для вставки шорткода:', 'litres-widget'); ?></p>
      
      <p><img class="imglitres" alt="" src="<?php echo $purl . '/img/about.png'; ?>" /></p>
      
      <p><?php _e('Новый редактор движка Gutenberg не поддерживается - в нем используйте блок "Шорткод" (в разделе "Виджеты").', 'litres-widget'); ?></p>
      <input style="left:-2000px;position: absolute;" type="text" value="[litres author='Автор книги' title='Название книги']" id="copy">
      <p><?php _e('Шаблон шорткода: <span style="color:#183691;">[litres author=\'</span><span style="color:green;">Автор книги</span><span style="color:#183691;">\' title=\'</span><span style="color:green;">Название книги</span><span style="color:#183691;">\']</span>', 'litres-widget'); ?> <button onclick="copyCode()" class="buttoncopy"><?php _e('Копировать в буфер', 'litres-widget'); ?></button><span id="tooltip"><?php _e('Скопировано', 'litres-widget'); ?></span></p>
      <p><?php _e('Все параметры обязательны (иначе виджет не будет выведен).', 'litres-widget'); ?></p> 
      </p>
     
      <br /><p><?php _e('Если вам нравится мой плагин, то, пожалуйста, поставьте ему <a target="new" href="https://ru.wordpress.org/plugins/litres-widget/"><strong>5 звезд</strong></a> в репозитории.', 'litres-widget'); ?></p>
      <p style="margin-top:20px;margin-bottom:10px;"><?php _e('Возможно, что вам также будут интересны другие мои плагины:', 'litres-widget'); ?></p>
      
      <div class="about">
        <ul>
            <li><a target="new" href="https://ru.wordpress.org/plugins/rss-for-yandex-zen/">RSS for Yandex Zen</a> - <?php _e('создание RSS-ленты для сервиса Яндекс.Дзен.', 'litres-widget'); ?></li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/rss-for-yandex-turbo/">RSS for Yandex Turbo</a> - <?php _e('создание RSS-ленты для сервиса Яндекс.Турбо.', 'litres-widget'); ?></li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/bbspoiler/">BBSpoiler</a> - <?php _e('плагин позволит вам спрятать текст под тегами [spoiler]текст[/spoiler].', 'litres-widget'); ?></li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/easy-textillate/">Easy Textillate</a> - <?php _e('плагин очень красиво анимирует текст (шорткодами в записях и виджетах или PHP-кодом в файлах темы).', 'litres-widget'); ?> </li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/cool-image-share/">Cool Image Share</a> - <?php _e('плагин добавляет иконки социальных сетей на каждое изображение в ваших записях.', 'litres-widget'); ?> </li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/today-yesterday-dates/">Today-Yesterday Dates</a> - <?php _e('относительные даты для записей за сегодня и вчера.', 'litres-widget'); ?> </li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/truncate-comments/">Truncate Comments</a> - <?php _e('плагин скрывает длинные комментарии js-скриптом (в стиле Яндекса или Амазона).', 'litres-widget'); ?> </li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/easy-yandex-share/">Easy Yandex Share</a> - <?php _e('продвинутый вывод блока "Яндекс.Поделиться".', 'rss-for-yandex-turbo'); ?></li>
            </ul>
      </div>     
    </div>
</div>
<?php wp_nonce_field( plugin_basename(__FILE__), 'litreswidget_nonce'); ?>
</form>
</div>
</div>
<?php 
}
//функция вывода страницы настроек плагина end

//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" begin
function litreswidget_menu() {
	add_options_page('LitRes Widget', 'LitRes Widget', 'manage_options', 'litres-widget.php', 'litreswidget_options_page');
}
add_action('admin_menu', 'litreswidget_menu');
//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" end

//исправление косяков стилей различных тем begin
function litreswidget_print_style() {
?>
<style>
.litres-banner-line td {border: none!important;text-align: left!important;padding: 0!important;background-color: initial!important;width:auto!important;vertical-align: middle!important;line-height: 16px!important;box-sizing: unset!important;}
.litres-banner-line td svg{margin-top: 2px!important;font-family: Arial!important;box-sizing: unset!important;}
.litres-banner-text{margin-top: 0px!important;}
.litres-banner-buy-block{margin-top: 2px!important;}
.mylitres .hidden {display:none!important;}
.mylitres {margin-bottom:10px;}
.mylitres table {margin:0px!important;line-height: 1!important;border: none!important;table-layout: initial!important;}
.mylitres table tr{border: none!important;}
</style>
<?php } 
add_action('wp_head', 'litreswidget_print_style');
add_action('admin_head', 'litreswidget_print_style');
//исправление косяков стилей различных тем end

//добавление кнопки в классический редактор движка begin 
function litreswidget_quicktags(){
if (wp_script_is('quicktags')){ ?>
<script type="text/javascript" charset="utf-8">
buttonLitres = edButtons.length;
edButtons[edButtons.length] = new edButton('litres','litres','[litres author=\'Автор книги\' title=\'Название книги\']\n');

jQuery(document).ready(function($){
    jQuery("#ed_toolbar").append('<input type="button" value="litres" id="ed_litres" class="ed_button" onclick="edInsertTag(edCanvas, buttonLitres);" title="LitRes Widget" />');
});
</script>
<?php } }
add_action('admin_print_footer_scripts', 'litreswidget_quicktags');

function litreswidget_add_tinymce() {
    global $typenow;
    if(!in_array($typenow, array('post', 'page')))
        return ;
    add_filter('mce_external_plugins', 'litreswidget_add_tinymce_plugin');
    add_filter('mce_buttons', 'litreswidget_add_tinymce_button');
}
add_action('admin_head', 'litreswidget_add_tinymce');

function litreswidget_add_tinymce_plugin($plugin_array) {
	$plugin_array['litres_button'] = plugins_url('/inc/litres.js', __FILE__);
    return $plugin_array;
}

// Add the button key for address via JS
function litreswidget_add_tinymce_button($buttons) {
    array_push($buttons, 'litres_button_button_key');
    return $buttons;
}
//добавление кнопки в классический редактор движка end

//шорткод плагина [litres] begin
function litres_shortcode($atts, $content) {
    extract(shortcode_atts(array(
		'author' => '',
        'title' => '',
	), $atts));

$litreswidget_options = get_option('litreswidget_options');   
if ( is_admin() && !$litreswidget_options['lfrom'] ) {$litreswidget_options['lfrom'] = '8878085';} 

if ($litreswidget_options['lfrom']) {    
$output = '
<div class="mylitres">
<div class="hidden" data-widget-litres-author>'.$author.'</div>
<div class="hidden" data-widget-litres-book>'.$title.'</div>
<script type="text/litres">
 lfrom: '.$litreswidget_options['lfrom'].'
</script>
</div>
'; 
} else {
$output = '
<div class="mylitres">
<p><span style="color:red;">Ошибка!</span> В настройках плагина «LitRes Widget» не указан «Партнерский ID».</p>
</div>
';     
}    
    
return $output;
}
add_shortcode('litres', 'litres_shortcode');
//шорткод плагина [litres] end

//включаем выполнение шорткодов в виджетах begin
add_filter('widget_text', 'do_shortcode');
//включаем выполнение шорткодов в виджетах end