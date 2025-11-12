<?php
/**
 * Plugin Name: RGBee Fly Buttons Feedback
 * Description: Плавающие кнопки обратной связи для WhatsApp, Telegram, Viber и форм обратной связи
 * Version: 1.1.0
 * Author: Александр Курков
 * Author URI: https://rgbee.ru
 * Text Domain: rgbee-fly-buttons-feedback
 */

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

// Константы плагина
define('FLY_BUTTONS_VERSION', '1.1.0');
define('FLY_BUTTONS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FLY_BUTTONS_PLUGIN_PATH', plugin_dir_path(__FILE__));

class FlyButtonsFeedback {
    
    private $options;
    
    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    public function init() {
        // Инициализация настроек
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Загрузка стилей и скриптов
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Добавление кнопок на фронтенд
        add_action('wp_footer', array($this, 'display_fly_buttons'));
        
        // Загрузка текстового домена
        load_plugin_textdomain('fly-buttons-feedback', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function admin_init() {
        register_setting(
            'fly_buttons_options',
            'fly_buttons_settings',
            array($this, 'sanitize_settings')
        );
        
        // Секция социальных сетей
        add_settings_section(
            'fly_buttons_social_section',
            __('Social Media Settings', 'fly-buttons-feedback'),
            array($this, 'social_section_callback'),
            'fly_buttons_admin'
        );
        
        // Секция кнопок обратной связи
        add_settings_section(
            'fly_buttons_feedback_section',
            __('Feedback Buttons Settings', 'fly-buttons-feedback'),
            array($this, 'feedback_section_callback'),
            'fly_buttons_admin'
        );
        
        // Секция стилей
        add_settings_section(
            'fly_buttons_style_section',
            __('Style Settings', 'fly-buttons-feedback'),
            array($this, 'style_section_callback'),
            'fly_buttons_admin'
        );
        
        // Поля для социальных сетей
        $this->add_social_fields();
        
        // Поля для кнопок обратной связи
        $this->add_feedback_fields();
        
        // Поля для стилей
        $this->add_style_fields();
    }
    
    private function add_social_fields() {
        add_settings_field(
            'whatsapp_phone',
            __('WhatsApp Phone Number', 'fly-buttons-feedback'),
            array($this, 'whatsapp_phone_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        
        add_settings_field(
            'whatsapp_text',
            __('WhatsApp Message Text', 'fly-buttons-feedback'),
            array($this, 'whatsapp_text_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        
        add_settings_field(
            'telegram_link',
            __('Telegram Link', 'fly-buttons-feedback'),
            array($this, 'telegram_link_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        
        add_settings_field(
            'telegram_title',
            __('Telegram Title', 'fly-buttons-feedback'),
            array($this, 'telegram_title_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        
        add_settings_field(
            'viber_phone',
            __('Viber Phone Number', 'fly-buttons-feedback'),
            array($this, 'viber_phone_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
    }
    
    private function add_feedback_fields() {
        // Кнопка "Заказать звонок"
        add_settings_field(
            'call_enabled',
            __('Enable Callback Button', 'fly-buttons-feedback'),
            array($this, 'call_enabled_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'call_title',
            __('Callback Button Title', 'fly-buttons-feedback'),
            array($this, 'call_title_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'call_link',
            __('Callback Button Link', 'fly-buttons-feedback'),
            array($this, 'call_link_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'call_attributes',
            __('Callback Button Attributes', 'fly-buttons-feedback'),
            array($this, 'call_attributes_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'call_custom_class',
            __('Callback Button Custom Class', 'fly-buttons-feedback'),
            array($this, 'call_custom_class_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        // Кнопка "Написать сообщение"
        add_settings_field(
            'message_enabled',
            __('Enable Message Button', 'fly-buttons-feedback'),
            array($this, 'message_enabled_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'message_title',
            __('Message Button Title', 'fly-buttons-feedback'),
            array($this, 'message_title_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'message_link',
            __('Message Button Link', 'fly-buttons-feedback'),
            array($this, 'message_link_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'message_attributes',
            __('Message Button Attributes', 'fly-buttons-feedback'),
            array($this, 'message_attributes_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'message_custom_class',
            __('Message Button Custom Class', 'fly-buttons-feedback'),
            array($this, 'message_custom_class_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        // Кнопка "Оставить отзыв"
        add_settings_field(
            'review_enabled',
            __('Enable Review Button', 'fly-buttons-feedback'),
            array($this, 'review_enabled_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'review_title',
            __('Review Button Title', 'fly-buttons-feedback'),
            array($this, 'review_title_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'review_link',
            __('Review Button Link', 'fly-buttons-feedback'),
            array($this, 'review_link_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'review_attributes',
            __('Review Button Attributes', 'fly-buttons-feedback'),
            array($this, 'review_attributes_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        add_settings_field(
            'review_custom_class',
            __('Review Button Custom Class', 'fly-buttons-feedback'),
            array($this, 'review_custom_class_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
    }
    
    private function add_style_fields() {
        add_settings_field(
            'default_color',
            __('Default Icon Color', 'fly-buttons-feedback'),
            array($this, 'default_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        
        add_settings_field(
            'hover_color',
            __('Hover Color', 'fly-buttons-feedback'),
            array($this, 'hover_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        
        add_settings_field(
            'whatsapp_color',
            __('WhatsApp Color', 'fly-buttons-feedback'),
            array($this, 'whatsapp_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        
        add_settings_field(
            'telegram_color',
            __('Telegram Color', 'fly-buttons-feedback'),
            array($this, 'telegram_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        
        add_settings_field(
            'viber_color',
            __('Viber Color', 'fly-buttons-feedback'),
            array($this, 'viber_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        
        // Цвета для кнопок обратной связи
        add_settings_field(
            'call_color',
            __('Callback Button Color', 'fly-buttons-feedback'),
            array($this, 'call_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        
        add_settings_field(
            'message_color',
            __('Message Button Color', 'fly-buttons-feedback'),
            array($this, 'message_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        
        add_settings_field(
            'review_color',
            __('Review Button Color', 'fly-buttons-feedback'),
            array($this, 'review_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
    }
    
    public function add_admin_menu() {
        add_options_page(
            __('Fly Buttons Settings', 'fly-buttons-feedback'),
            __('Fly Buttons', 'fly-buttons-feedback'),
            'manage_options',
            'fly-buttons-settings',
            array($this, 'options_page')
        );
    }
    
    public function options_page() {
        $this->options = get_option('fly_buttons_settings');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Fly Buttons Feedback Settings', 'fly-buttons-feedback'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('fly_buttons_options');
                do_settings_sections('fly_buttons_admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    public function sanitize_settings($input) {
        $sanitized_input = array();
        
        // Санитизация полей социальных сетей
        $sanitized_input['whatsapp_phone'] = sanitize_text_field($input['whatsapp_phone']);
        $sanitized_input['whatsapp_text'] = sanitize_text_field($input['whatsapp_text']);
        $sanitized_input['telegram_link'] = esc_url_raw($input['telegram_link']);
        $sanitized_input['telegram_title'] = sanitize_text_field($input['telegram_title']);
        $sanitized_input['viber_phone'] = sanitize_text_field($input['viber_phone']);
        
        // Санитизация настроек кнопок обратной связи
        $sanitized_input['call_enabled'] = isset($input['call_enabled']) ? 1 : 0;
        $sanitized_input['call_title'] = sanitize_text_field($input['call_title']);
        $sanitized_input['call_link'] = esc_url_raw($input['call_link']);
        $sanitized_input['call_attributes'] = sanitize_text_field($input['call_attributes']);
        $sanitized_input['call_custom_class'] = sanitize_text_field($input['call_custom_class']);
        
        $sanitized_input['message_enabled'] = isset($input['message_enabled']) ? 1 : 0;
        $sanitized_input['message_title'] = sanitize_text_field($input['message_title']);
        $sanitized_input['message_link'] = esc_url_raw($input['message_link']);
        $sanitized_input['message_attributes'] = sanitize_text_field($input['message_attributes']);
        $sanitized_input['message_custom_class'] = sanitize_text_field($input['message_custom_class']);
        
        $sanitized_input['review_enabled'] = isset($input['review_enabled']) ? 1 : 0;
        $sanitized_input['review_title'] = sanitize_text_field($input['review_title']);
        $sanitized_input['review_link'] = esc_url_raw($input['review_link']);
        $sanitized_input['review_attributes'] = sanitize_text_field($input['review_attributes']);
        $sanitized_input['review_custom_class'] = sanitize_text_field($input['review_custom_class']);
        
        // Санитизация цветов
        $sanitized_input['default_color'] = sanitize_hex_color($input['default_color']);
        $sanitized_input['hover_color'] = sanitize_hex_color($input['hover_color']);
        $sanitized_input['whatsapp_color'] = sanitize_hex_color($input['whatsapp_color']);
        $sanitized_input['telegram_color'] = sanitize_hex_color($input['telegram_color']);
        $sanitized_input['viber_color'] = sanitize_hex_color($input['viber_color']);
        $sanitized_input['call_color'] = sanitize_hex_color($input['call_color']);
        $sanitized_input['message_color'] = sanitize_hex_color($input['message_color']);
        $sanitized_input['review_color'] = sanitize_hex_color($input['review_color']);
        
        return $sanitized_input;
    }
    
    // Callback функции для секций
    public function social_section_callback() {
        echo '<p>' . esc_html__('Configure your social media contacts and messaging settings.', 'fly-buttons-feedback') . '</p>';
    }
    
    public function feedback_section_callback() {
        echo '<p>' . esc_html__('Configure feedback buttons settings - titles, links, attributes and visibility.', 'fly-buttons-feedback') . '</p>';
    }
    
    public function style_section_callback() {
        echo '<p>' . esc_html__('Customize the appearance of the fly buttons.', 'fly-buttons-feedback') . '</p>';
    }
    
    // Callback функции для социальных сетей (остаются без изменений)
    public function whatsapp_phone_callback() {
        $value = isset($this->options['whatsapp_phone']) ? $this->options['whatsapp_phone'] : '';
        echo '<input type="text" id="whatsapp_phone" name="fly_buttons_settings[whatsapp_phone]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Enter WhatsApp phone number with country code (e.g., 79123456789)', 'fly-buttons-feedback') . '</p>';
    }
    
    public function whatsapp_text_callback() {
        $value = isset($this->options['whatsapp_text']) ? $this->options['whatsapp_text'] : '';
        echo '<input type="text" id="whatsapp_text" name="fly_buttons_settings[whatsapp_text]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Predefined text for WhatsApp message', 'fly-buttons-feedback') . '</p>';
    }
    
    public function telegram_link_callback() {
        $value = isset($this->options['telegram_link']) ? $this->options['telegram_link'] : '';
        echo '<input type="url" id="telegram_link" name="fly_buttons_settings[telegram_link]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Enter your Telegram profile link', 'fly-buttons-feedback') . '</p>';
    }
    
    public function telegram_title_callback() {
        $value = isset($this->options['telegram_title']) ? $this->options['telegram_title'] : '';
        echo '<input type="text" id="telegram_title" name="fly_buttons_settings[telegram_title]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Title for Telegram button', 'fly-buttons-feedback') . '</p>';
    }
    
    public function viber_phone_callback() {
        $value = isset($this->options['viber_phone']) ? $this->options['viber_phone'] : '';
        echo '<input type="text" id="viber_phone" name="fly_buttons_settings[viber_phone]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Enter Viber phone number with country code', 'fly-buttons-feedback') . '</p>';
    }
    
    // Callback функции для кнопки "Заказать звонок"
    public function call_enabled_callback() {
        $value = isset($this->options['call_enabled']) ? $this->options['call_enabled'] : 1;
        echo '<input type="checkbox" id="call_enabled" name="fly_buttons_settings[call_enabled]" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="call_enabled">' . esc_html__('Enable callback button', 'fly-buttons-feedback') . '</label>';
    }
    
    public function call_title_callback() {
        $value = isset($this->options['call_title']) ? $this->options['call_title'] : __('Заказать звонок', 'fly-buttons-feedback');
        echo '<input type="text" id="call_title" name="fly_buttons_settings[call_title]" value="' . esc_attr($value) . '" class="regular-text" />';
    }
    
    public function call_link_callback() {
        $value = isset($this->options['call_link']) ? $this->options['call_link'] : '#';
        echo '<input type="text" id="call_link" name="fly_buttons_settings[call_link]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Link for callback button (can be # for modal window)', 'fly-buttons-feedback') . '</p>';
    }
    
    public function call_attributes_callback() {
        $value = isset($this->options['call_attributes']) ? $this->options['call_attributes'] : 'data-bs-toggle="modal" data-bs-target="#callbackModal"';
        echo '<textarea id="call_attributes" name="fly_buttons_settings[call_attributes]" class="large-text" rows="3">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . esc_html__('Custom attributes for callback button (e.g., data-bs-toggle="modal" data-bs-target="#callbackModal")', 'fly-buttons-feedback') . '</p>';
    }
    
    public function call_custom_class_callback() {
        $value = isset($this->options['call_custom_class']) ? $this->options['call_custom_class'] : '';
        echo '<input type="text" id="call_custom_class" name="fly_buttons_settings[call_custom_class]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Custom CSS class for callback button', 'fly-buttons-feedback') . '</p>';
    }
    
    // Callback функции для кнопки "Написать сообщение"
    public function message_enabled_callback() {
        $value = isset($this->options['message_enabled']) ? $this->options['message_enabled'] : 1;
        echo '<input type="checkbox" id="message_enabled" name="fly_buttons_settings[message_enabled]" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="message_enabled">' . esc_html__('Enable message button', 'fly-buttons-feedback') . '</label>';
    }
    
    public function message_title_callback() {
        $value = isset($this->options['message_title']) ? $this->options['message_title'] : __('Написать сообщение', 'fly-buttons-feedback');
        echo '<input type="text" id="message_title" name="fly_buttons_settings[message_title]" value="' . esc_attr($value) . '" class="regular-text" />';
    }
    
    public function message_link_callback() {
        $value = isset($this->options['message_link']) ? $this->options['message_link'] : '#';
        echo '<input type="text" id="message_link" name="fly_buttons_settings[message_link]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Link for message button', 'fly-buttons-feedback') . '</p>';
    }
    
    public function message_attributes_callback() {
        $value = isset($this->options['message_attributes']) ? $this->options['message_attributes'] : 'data-bs-toggle="modal" data-bs-target="#messageModal"';
        echo '<textarea id="message_attributes" name="fly_buttons_settings[message_attributes]" class="large-text" rows="3">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . esc_html__('Custom attributes for message button', 'fly-buttons-feedback') . '</p>';
    }
    
    public function message_custom_class_callback() {
        $value = isset($this->options['message_custom_class']) ? $this->options['message_custom_class'] : '';
        echo '<input type="text" id="message_custom_class" name="fly_buttons_settings[message_custom_class]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Custom CSS class for message button', 'fly-buttons-feedback') . '</p>';
    }
    
    // Callback функции для кнопки "Оставить отзыв"
    public function review_enabled_callback() {
        $value = isset($this->options['review_enabled']) ? $this->options['review_enabled'] : 1;
        echo '<input type="checkbox" id="review_enabled" name="fly_buttons_settings[review_enabled]" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="review_enabled">' . esc_html__('Enable review button', 'fly-buttons-feedback') . '</label>';
    }
    
    public function review_title_callback() {
        $value = isset($this->options['review_title']) ? $this->options['review_title'] : __('Оставить отзыв', 'fly-buttons-feedback');
        echo '<input type="text" id="review_title" name="fly_buttons_settings[review_title]" value="' . esc_attr($value) . '" class="regular-text" />';
    }
    
    public function review_link_callback() {
        $value = isset($this->options['review_link']) ? $this->options['review_link'] : '#';
        echo '<input type="text" id="review_link" name="fly_buttons_settings[review_link]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Link for review button', 'fly-buttons-feedback') . '</p>';
    }
    
    public function review_attributes_callback() {
        $value = isset($this->options['review_attributes']) ? $this->options['review_attributes'] : 'data-bs-toggle="modal" data-bs-target="#reviewModal"';
        echo '<textarea id="review_attributes" name="fly_buttons_settings[review_attributes]" class="large-text" rows="3">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . esc_html__('Custom attributes for review button', 'fly-buttons-feedback') . '</p>';
    }
    
    public function review_custom_class_callback() {
        $value = isset($this->options['review_custom_class']) ? $this->options['review_custom_class'] : '';
        echo '<input type="text" id="review_custom_class" name="fly_buttons_settings[review_custom_class]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Custom CSS class for review button', 'fly-buttons-feedback') . '</p>';
    }
    
    // Callback функции для цветов (добавляем новые)
    public function default_color_callback() {
        $value = isset($this->options['default_color']) ? $this->options['default_color'] : '#ddd';
        echo '<input type="color" id="default_color" name="fly_buttons_settings[default_color]" value="' . esc_attr($value) . '" />';
    }
    
    public function hover_color_callback() {
        $value = isset($this->options['hover_color']) ? $this->options['hover_color'] : '#1a1a1a';
        echo '<input type="color" id="hover_color" name="fly_buttons_settings[hover_color]" value="' . esc_attr($value) . '" />';
    }
    
    public function whatsapp_color_callback() {
        $value = isset($this->options['whatsapp_color']) ? $this->options['whatsapp_color'] : '#2ab13f';
        echo '<input type="color" id="whatsapp_color" name="fly_buttons_settings[whatsapp_color]" value="' . esc_attr($value) . '" />';
    }
    
    public function telegram_color_callback() {
        $value = isset($this->options['telegram_color']) ? $this->options['telegram_color'] : '#25a3e2';
        echo '<input type="color" id="telegram_color" name="fly_buttons_settings[telegram_color]" value="' . esc_attr($value) . '" />';
    }
    
    public function viber_color_callback() {
        $value = isset($this->options['viber_color']) ? $this->options['viber_color'] : '#7a4f99';
        echo '<input type="color" id="viber_color" name="fly_buttons_settings[viber_color]" value="' . esc_attr($value) . '" />';
    }
    
    public function call_color_callback() {
        $value = isset($this->options['call_color']) ? $this->options['call_color'] : '#28a745';
        echo '<input type="color" id="call_color" name="fly_buttons_settings[call_color]" value="' . esc_attr($value) . '" />';
    }
    
    public function message_color_callback() {
        $value = isset($this->options['message_color']) ? $this->options['message_color'] : '#17a2b8';
        echo '<input type="color" id="message_color" name="fly_buttons_settings[message_color]" value="' . esc_attr($value) . '" />';
    }
    
    public function review_color_callback() {
        $value = isset($this->options['review_color']) ? $this->options['review_color'] : '#ffc107';
        echo '<input type="color" id="review_color" name="fly_buttons_settings[review_color]" value="' . esc_attr($value) . '" />';
    }
    
    public function enqueue_scripts() {
        // Подключаем Font Awesome
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css',
            array(),
            '6.2.1'
        );
        
        // Добавляем инлайн стили с пользовательскими цветами
        $this->add_custom_styles();
    }
    
    private function add_custom_styles() {
        $options = get_option('fly_buttons_settings');
        
        // Получаем цвета
        $default_color = isset($options['default_color']) ? $options['default_color'] : '#aaaaaa';
        $hover_color = isset($options['hover_color']) ? $options['hover_color'] : '#1a1a1a';
        $whatsapp_color = isset($options['whatsapp_color']) ? $options['whatsapp_color'] : '#2ab13f';
        $telegram_color = isset($options['telegram_color']) ? $options['telegram_color'] : '#25a3e2';
        $viber_color = isset($options['viber_color']) ? $options['viber_color'] : '#7a4f99';
        $call_color = isset($options['call_color']) ? $options['call_color'] : '#28a745';
        $message_color = isset($options['message_color']) ? $options['message_color'] : '#17a2b8';
        $review_color = isset($options['review_color']) ? $options['review_color'] : '#ffc107';
        
        $custom_css = "
        .fly_buttons {
            background: #fff;
            border: 1px solid #ddd;
            position: fixed;
            width: 60px;
            right: 0;
            top: 170px;
            left: auto;
            box-sizing: border-box;
            z-index: 999;
        }

        .fly_buttons .fly_item {
            border-bottom: 1px solid #ddd;
            box-sizing: border-box;
            width: 60px;
            height: 60px; 
            display: flex;
            align-items: center;
            font-size: 25px;
        }
        .fly_buttons .fly_item:last-child {border-bottom: none;}
        
        /* Общее правило для всех ссылок - убираем подчеркивание */
        .fly_buttons .fly_item a {
            width: 60px; 
            height: 60px; 
            box-sizing: border-box; 
            margin: auto; 
            color: {$default_color}; 
            display: flex; 
            align-items: center;
            text-decoration: none !important;
        }
        .fly_buttons .fly_item a:hover {
            color: {$hover_color};
            text-decoration: none !important;
        }
        .fly_buttons .fly_item a i {margin: 0 auto;}
        
        /* Цвета для социальных кнопок */
        .fly_buttons .fly_item a.wa {color: {$whatsapp_color};}
        .fly_buttons .fly_item a.tg {color: {$telegram_color};}
        .fly_buttons .fly_item a.vb {color: {$viber_color};}
        
        /* Цвета для кнопок обратной связи */
        .fly_buttons .fly_item a.call-btn {color: {$call_color};}
        .fly_buttons .fly_item a.message-btn {color: {$message_color};}
        .fly_buttons .fly_item a.review-btn {color: {$review_color};}
        
        .fly_buttons .fly_item a.vb:hover,
        .fly_buttons .fly_item a.wa:hover,
        .fly_buttons .fly_item a.tg:hover,
        .fly_buttons .fly_item a.call-btn:hover,
        .fly_buttons .fly_item a.message-btn:hover,
        .fly_buttons .fly_item a.review-btn:hover {color: {$hover_color};}

        @media (max-width: 767px) {
            .fly_buttons {
                bottom: 0; 
                left: 0; 
                top: auto; 
                right: auto; 
                width: 100%; 
                height: 60px; 
                display: flex; 
                border-width: 1px 0 0 0;
            }
            .fly_buttons .fly_item {
                width: 33%; 
                margin: 0 auto; 
                border-bottom: none; 
            }
        }";
        
        wp_add_inline_style('font-awesome', $custom_css);
    }
    
    public function display_fly_buttons() {
        $options = get_option('fly_buttons_settings');
        
        echo '<div class="fly_buttons">';
        
        // WhatsApp
        if (!empty($options['whatsapp_phone'])) {
            $whatsapp_phone_number = preg_replace('/[^0-9]/', '', $options['whatsapp_phone']);
            $whatsapp_text = !empty($options['whatsapp_text']) ? urlencode($options['whatsapp_text']) : '';
            echo '<div class="fly_item"><a href="https://wa.me/' . esc_attr($whatsapp_phone_number) . '?text=' . esc_attr($whatsapp_text) . '" title="' . esc_attr__('Написать в WhatsApp', 'fly-buttons-feedback') . '" target="_blank" class="wa"><i class="fa-brands fa-square-whatsapp"></i></a></div>';
        }
        
        // Telegram
        if (!empty($options['telegram_link'])) {
            $tg_title = !empty($options['telegram_title']) ? $options['telegram_title'] : $options['telegram_link'];
            echo '<div class="fly_item"><a href="' . esc_url($options['telegram_link']) . '" title="' . esc_attr__('Написать в Telegram', 'fly-buttons-feedback') . '" target="_blank" class="tg"><i class="fa-brands fa-telegram"></i></a></div>';
        }
        
        // Viber
        if (!empty($options['viber_phone'])) {
            $viber_phone_number = preg_replace('/[^0-9]/', '', $options['viber_phone']);
            echo '<div class="fly_item"><a href="viber://chat?number=+' . esc_attr($viber_phone_number) . '" title="' . esc_attr__('Написать в Viber', 'fly-buttons-feedback') . '" target="_blank" class="vb"><i class="fa-brands fa-viber"></i></a></div>';
        }
        
        // Кнопка "Заказать звонок"
        if (isset($options['call_enabled']) && $options['call_enabled']) {
            $call_title = !empty($options['call_title']) ? $options['call_title'] : __('Заказать звонок', 'fly-buttons-feedback');
            $call_link = !empty($options['call_link']) ? $options['call_link'] : '#';
            $call_attributes = !empty($options['call_attributes']) ? $options['call_attributes'] : '';
            $call_custom_class = !empty($options['call_custom_class']) ? ' ' . $options['call_custom_class'] : '';
            
            echo '<div class="fly_item"><a href="' . esc_url($call_link) . '" title="' . esc_attr($call_title) . '" class="call-btn' . esc_attr($call_custom_class) . '" ' . $call_attributes . '><i class="fa-solid fa-phone"></i></a></div>';
        }
        
        // Кнопка "Написать сообщение"
        if (isset($options['message_enabled']) && $options['message_enabled']) {
            $message_title = !empty($options['message_title']) ? $options['message_title'] : __('Написать сообщение', 'fly-buttons-feedback');
            $message_link = !empty($options['message_link']) ? $options['message_link'] : '#';
            $message_attributes = !empty($options['message_attributes']) ? $options['message_attributes'] : '';
            $message_custom_class = !empty($options['message_custom_class']) ? ' ' . $options['message_custom_class'] : '';
            
            echo '<div class="fly_item"><a href="' . esc_url($message_link) . '" title="' . esc_attr($message_title) . '" class="message-btn' . esc_attr($message_custom_class) . '" ' . $message_attributes . '><i class="fa-solid fa-envelope"></i></a></div>';
        }
        
        // Кнопка "Оставить отзыв"
        if (isset($options['review_enabled']) && $options['review_enabled']) {
            $review_title = !empty($options['review_title']) ? $options['review_title'] : __('Оставить отзыв', 'fly-buttons-feedback');
            $review_link = !empty($options['review_link']) ? $options['review_link'] : '#';
            $review_attributes = !empty($options['review_attributes']) ? $options['review_attributes'] : '';
            $review_custom_class = !empty($options['review_custom_class']) ? ' ' . $options['review_custom_class'] : '';
            
            echo '<div class="fly_item"><a href="' . esc_url($review_link) . '" title="' . esc_attr($review_title) . '" class="review-btn' . esc_attr($review_custom_class) . '" ' . $review_attributes . '><i class="fa-solid fa-comment-dots"></i></a></div>';
        }
        
        echo '</div>';
    }
}

// Инициализация плагина
new FlyButtonsFeedback();
?>