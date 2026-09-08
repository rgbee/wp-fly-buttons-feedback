<?php
/**
 * Plugin Name: RGBee Fly Buttons Feedback
 * Description: Плавающие кнопки обратной связи для WhatsApp, Telegram, Viber, Max и форм обратной связи
 * Version: 1.4.0
 * Author: Александр Курков
 * Author URI: https://rgbee.ru
 * Text Domain: rgbee-fly-buttons-feedback
 */

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

// Константы плагина
define('FLY_BUTTONS_VERSION', '1.4.0');
define('FLY_BUTTONS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FLY_BUTTONS_PLUGIN_PATH', plugin_dir_path(__FILE__));

class FlyButtonsFeedback {
    
    private $options;
    
    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
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
        load_plugin_textdomain('rgbee-fly-buttons-feedback', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function activate() {
        // Создаем необходимые папки при активации плагина
        $this->create_fontawesome_folders();
    }
    
    private function create_fontawesome_folders() {
        $fontawesome_dir = FLY_BUTTONS_PLUGIN_PATH . 'assets/fontawesome';
        $css_dir = $fontawesome_dir . '/css';
        $webfonts_dir = $fontawesome_dir . '/webfonts';
        
        // Создаем папки если их нет
        if (!file_exists($fontawesome_dir)) {
            wp_mkdir_p($fontawesome_dir);
        }
        if (!file_exists($css_dir)) {
            wp_mkdir_p($css_dir);
        }
        if (!file_exists($webfonts_dir)) {
            wp_mkdir_p($webfonts_dir);
        }
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
            __('Social Media Settings', 'rgbee-fly-buttons-feedback'),
            array($this, 'social_section_callback'),
            'fly_buttons_admin'
        );
        
        // Секция кнопок обратной связи
        add_settings_section(
            'fly_buttons_feedback_section',
            __('Feedback Buttons Settings', 'rgbee-fly-buttons-feedback'),
            array($this, 'feedback_section_callback'),
            'fly_buttons_admin'
        );
        
        // Секция стилей
        add_settings_section(
            'fly_buttons_style_section',
            __('Style Settings', 'rgbee-fly-buttons-feedback'),
            array($this, 'style_section_callback'),
            'fly_buttons_admin'
        );
        
        // Секция Яндекс.Метрики
        add_settings_section(
            'fly_buttons_metrika_section',
            __('Яндекс.Метрика', 'rgbee-fly-buttons-feedback'),
            array($this, 'metrika_section_callback'),
            'fly_buttons_admin'
        );
        
        // Поля для социальных сетей
        $this->add_social_fields();
        
        // Поля для кнопок обратной связи
        $this->add_feedback_fields();
        
        // Поля для стилей
        $this->add_style_fields();
        
        // Поле для номера счётчика Метрики
        add_settings_field(
            'metrika_counter',
            __('Номер счётчика', 'rgbee-fly-buttons-feedback'),
            array($this, 'metrika_counter_callback'),
            'fly_buttons_admin',
            'fly_buttons_metrika_section'
        );
    }
    
    private function add_social_fields() {
        // WhatsApp
        add_settings_field(
            'whatsapp_phone',
            __('WhatsApp Phone Number', 'rgbee-fly-buttons-feedback'),
            array($this, 'whatsapp_phone_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        add_settings_field(
            'whatsapp_text',
            __('WhatsApp Message Text', 'rgbee-fly-buttons-feedback'),
            array($this, 'whatsapp_text_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        add_settings_field(
            'whatsapp_title',
            __('WhatsApp Title', 'rgbee-fly-buttons-feedback'),
            array($this, 'whatsapp_title_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        add_settings_field(
            'whatsapp_goal',
            __('WhatsApp Goal ID', 'rgbee-fly-buttons-feedback'),
            array($this, 'whatsapp_goal_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        
        // Telegram
        add_settings_field(
            'telegram_link',
            __('Telegram Link', 'rgbee-fly-buttons-feedback'),
            array($this, 'telegram_link_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        add_settings_field(
            'telegram_title',
            __('Telegram Title', 'rgbee-fly-buttons-feedback'),
            array($this, 'telegram_title_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        add_settings_field(
            'telegram_goal',
            __('Telegram Goal ID', 'rgbee-fly-buttons-feedback'),
            array($this, 'telegram_goal_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        
        // Viber
        add_settings_field(
            'viber_phone',
            __('Viber Phone Number', 'rgbee-fly-buttons-feedback'),
            array($this, 'viber_phone_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        add_settings_field(
            'viber_title',
            __('Viber Title', 'rgbee-fly-buttons-feedback'),
            array($this, 'viber_title_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        add_settings_field(
            'viber_goal',
            __('Viber Goal ID', 'rgbee-fly-buttons-feedback'),
            array($this, 'viber_goal_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        
        // Max
        add_settings_field(
            'max_link',
            __('Max Link', 'rgbee-fly-buttons-feedback'),
            array($this, 'max_link_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        add_settings_field(
            'max_title',
            __('Max Title', 'rgbee-fly-buttons-feedback'),
            array($this, 'max_title_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
        add_settings_field(
            'max_goal',
            __('Max Goal ID', 'rgbee-fly-buttons-feedback'),
            array($this, 'max_goal_callback'),
            'fly_buttons_admin',
            'fly_buttons_social_section'
        );
    }
    
    private function add_feedback_fields() {
        // Кнопка "Заказать звонок"
        add_settings_field(
            'call_enabled',
            __('Enable Callback Button', 'rgbee-fly-buttons-feedback'),
            array($this, 'call_enabled_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'call_title',
            __('Callback Button Title', 'rgbee-fly-buttons-feedback'),
            array($this, 'call_title_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'call_link',
            __('Callback Button Link', 'rgbee-fly-buttons-feedback'),
            array($this, 'call_link_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'call_attributes',
            __('Callback Button Attributes', 'rgbee-fly-buttons-feedback'),
            array($this, 'call_attributes_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'call_custom_class',
            __('Callback Button Custom Class', 'rgbee-fly-buttons-feedback'),
            array($this, 'call_custom_class_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'call_goal',
            __('Callback Goal ID', 'rgbee-fly-buttons-feedback'),
            array($this, 'call_goal_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        // Кнопка "Написать сообщение"
        add_settings_field(
            'message_enabled',
            __('Enable Message Button', 'rgbee-fly-buttons-feedback'),
            array($this, 'message_enabled_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'message_title',
            __('Message Button Title', 'rgbee-fly-buttons-feedback'),
            array($this, 'message_title_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'message_link',
            __('Message Button Link', 'rgbee-fly-buttons-feedback'),
            array($this, 'message_link_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'message_attributes',
            __('Message Button Attributes', 'rgbee-fly-buttons-feedback'),
            array($this, 'message_attributes_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'message_custom_class',
            __('Message Button Custom Class', 'rgbee-fly-buttons-feedback'),
            array($this, 'message_custom_class_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'message_goal',
            __('Message Goal ID', 'rgbee-fly-buttons-feedback'),
            array($this, 'message_goal_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        
        // Кнопка "Оставить отзыв"
        add_settings_field(
            'review_enabled',
            __('Enable Review Button', 'rgbee-fly-buttons-feedback'),
            array($this, 'review_enabled_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'review_title',
            __('Review Button Title', 'rgbee-fly-buttons-feedback'),
            array($this, 'review_title_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'review_link',
            __('Review Button Link', 'rgbee-fly-buttons-feedback'),
            array($this, 'review_link_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'review_attributes',
            __('Review Button Attributes', 'rgbee-fly-buttons-feedback'),
            array($this, 'review_attributes_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'review_custom_class',
            __('Review Button Custom Class', 'rgbee-fly-buttons-feedback'),
            array($this, 'review_custom_class_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
        add_settings_field(
            'review_goal',
            __('Review Goal ID', 'rgbee-fly-buttons-feedback'),
            array($this, 'review_goal_callback'),
            'fly_buttons_admin',
            'fly_buttons_feedback_section'
        );
    }
    
    private function add_style_fields() {
        add_settings_field(
            'default_color',
            __('Default Icon Color', 'rgbee-fly-buttons-feedback'),
            array($this, 'default_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        add_settings_field(
            'hover_color',
            __('Hover Color', 'rgbee-fly-buttons-feedback'),
            array($this, 'hover_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        add_settings_field(
            'whatsapp_color',
            __('WhatsApp Color', 'rgbee-fly-buttons-feedback'),
            array($this, 'whatsapp_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        add_settings_field(
            'telegram_color',
            __('Telegram Color', 'rgbee-fly-buttons-feedback'),
            array($this, 'telegram_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        add_settings_field(
            'viber_color',
            __('Viber Color', 'rgbee-fly-buttons-feedback'),
            array($this, 'viber_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        add_settings_field(
            'call_color',
            __('Callback Button Color', 'rgbee-fly-buttons-feedback'),
            array($this, 'call_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        add_settings_field(
            'message_color',
            __('Message Button Color', 'rgbee-fly-buttons-feedback'),
            array($this, 'message_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
        add_settings_field(
            'review_color',
            __('Review Button Color', 'rgbee-fly-buttons-feedback'),
            array($this, 'review_color_callback'),
            'fly_buttons_admin',
            'fly_buttons_style_section'
        );
    }
    
    public function add_admin_menu() {
        add_options_page(
            __('Fly Buttons Settings', 'rgbee-fly-buttons-feedback'),
            __('Fly Buttons', 'rgbee-fly-buttons-feedback'),
            'manage_options',
            'fly-buttons-settings',
            array($this, 'options_page')
        );
    }
    
    public function options_page() {
        $this->options = get_option('fly_buttons_settings');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Fly Buttons Feedback Settings', 'rgbee-fly-buttons-feedback'); ?></h1>
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
        
        // Социальные сети
        $sanitized_input['whatsapp_phone'] = sanitize_text_field($input['whatsapp_phone']);
        $sanitized_input['whatsapp_text'] = sanitize_text_field($input['whatsapp_text']);
        $sanitized_input['whatsapp_title'] = sanitize_text_field($input['whatsapp_title']);
        $sanitized_input['whatsapp_goal'] = sanitize_text_field($input['whatsapp_goal']);
        
        $sanitized_input['telegram_link'] = esc_url_raw($input['telegram_link']);
        $sanitized_input['telegram_title'] = sanitize_text_field($input['telegram_title']);
        $sanitized_input['telegram_goal'] = sanitize_text_field($input['telegram_goal']);
        
        $sanitized_input['viber_phone'] = sanitize_text_field($input['viber_phone']);
        $sanitized_input['viber_title'] = sanitize_text_field($input['viber_title']);
        $sanitized_input['viber_goal'] = sanitize_text_field($input['viber_goal']);
        
        $sanitized_input['max_link'] = esc_url_raw($input['max_link']);
        $sanitized_input['max_title'] = sanitize_text_field($input['max_title']);
        $sanitized_input['max_goal'] = sanitize_text_field($input['max_goal']);
        
        // Кнопки обратной связи
        $sanitized_input['call_enabled'] = isset($input['call_enabled']) ? 1 : 0;
        $sanitized_input['call_title'] = sanitize_text_field($input['call_title']);
        $sanitized_input['call_link'] = esc_url_raw($input['call_link']);
        $sanitized_input['call_attributes'] = sanitize_text_field($input['call_attributes']);
        $sanitized_input['call_custom_class'] = sanitize_text_field($input['call_custom_class']);
        $sanitized_input['call_goal'] = sanitize_text_field($input['call_goal']);
        
        $sanitized_input['message_enabled'] = isset($input['message_enabled']) ? 1 : 0;
        $sanitized_input['message_title'] = sanitize_text_field($input['message_title']);
        $sanitized_input['message_link'] = esc_url_raw($input['message_link']);
        $sanitized_input['message_attributes'] = sanitize_text_field($input['message_attributes']);
        $sanitized_input['message_custom_class'] = sanitize_text_field($input['message_custom_class']);
        $sanitized_input['message_goal'] = sanitize_text_field($input['message_goal']);
        
        $sanitized_input['review_enabled'] = isset($input['review_enabled']) ? 1 : 0;
        $sanitized_input['review_title'] = sanitize_text_field($input['review_title']);
        $sanitized_input['review_link'] = esc_url_raw($input['review_link']);
        $sanitized_input['review_attributes'] = sanitize_text_field($input['review_attributes']);
        $sanitized_input['review_custom_class'] = sanitize_text_field($input['review_custom_class']);
        $sanitized_input['review_goal'] = sanitize_text_field($input['review_goal']);
        
        // Метрика
        $sanitized_input['metrika_counter'] = sanitize_text_field($input['metrika_counter']);
        
        // Цвета
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
    
    // ------ Callback для секций ------
    public function social_section_callback() {
        echo '<p>' . esc_html__('Configure your social media contacts and messaging settings.', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function feedback_section_callback() {
        echo '<p>' . esc_html__('Configure feedback buttons settings - titles, links, attributes and visibility.', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function style_section_callback() {
        echo '<p>' . esc_html__('Customize the appearance of the fly buttons.', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function metrika_section_callback() {
        echo '<p>' . esc_html__('Настройки интеграции с Яндекс.Метрикой. Если номер счётчика не указан, события не отправляются.', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    // ------ Callback для социальных сетей (поля) ------
    public function whatsapp_phone_callback() {
        $value = isset($this->options['whatsapp_phone']) ? $this->options['whatsapp_phone'] : '';
        echo '<input type="text" id="whatsapp_phone" name="fly_buttons_settings[whatsapp_phone]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Enter WhatsApp phone number with country code (e.g., 79123456789)', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function whatsapp_text_callback() {
        $value = isset($this->options['whatsapp_text']) ? $this->options['whatsapp_text'] : '';
        echo '<input type="text" id="whatsapp_text" name="fly_buttons_settings[whatsapp_text]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Predefined text for WhatsApp message', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function whatsapp_title_callback() {
        $value = isset($this->options['whatsapp_title']) ? $this->options['whatsapp_title'] : __('Написать в WhatsApp', 'rgbee-fly-buttons-feedback');
        echo '<input type="text" id="whatsapp_title" name="fly_buttons_settings[whatsapp_title]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Title for WhatsApp button', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function whatsapp_goal_callback() {
        $value = isset($this->options['whatsapp_goal']) ? $this->options['whatsapp_goal'] : '';
        echo '<input type="text" id="whatsapp_goal" name="fly_buttons_settings[whatsapp_goal]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Идентификатор цели для клика по WhatsApp', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function telegram_link_callback() {
        $value = isset($this->options['telegram_link']) ? $this->options['telegram_link'] : '';
        echo '<input type="url" id="telegram_link" name="fly_buttons_settings[telegram_link]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Enter your Telegram profile link', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function telegram_title_callback() {
        $value = isset($this->options['telegram_title']) ? $this->options['telegram_title'] : __('Написать в Telegram', 'rgbee-fly-buttons-feedback');
        echo '<input type="text" id="telegram_title" name="fly_buttons_settings[telegram_title]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Title for Telegram button', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function telegram_goal_callback() {
        $value = isset($this->options['telegram_goal']) ? $this->options['telegram_goal'] : '';
        echo '<input type="text" id="telegram_goal" name="fly_buttons_settings[telegram_goal]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Идентификатор цели для клика по Telegram', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function viber_phone_callback() {
        $value = isset($this->options['viber_phone']) ? $this->options['viber_phone'] : '';
        echo '<input type="text" id="viber_phone" name="fly_buttons_settings[viber_phone]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Enter Viber phone number with country code', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function viber_title_callback() {
        $value = isset($this->options['viber_title']) ? $this->options['viber_title'] : __('Написать в Viber', 'rgbee-fly-buttons-feedback');
        echo '<input type="text" id="viber_title" name="fly_buttons_settings[viber_title]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Title for Viber button', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function viber_goal_callback() {
        $value = isset($this->options['viber_goal']) ? $this->options['viber_goal'] : '';
        echo '<input type="text" id="viber_goal" name="fly_buttons_settings[viber_goal]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Идентификатор цели для клика по Viber', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function max_link_callback() {
        $value = isset($this->options['max_link']) ? $this->options['max_link'] : '';
        echo '<input type="url" id="max_link" name="fly_buttons_settings[max_link]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Enter your Max profile link', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function max_title_callback() {
        $value = isset($this->options['max_title']) ? $this->options['max_title'] : __('Написать в Max', 'rgbee-fly-buttons-feedback');
        echo '<input type="text" id="max_title" name="fly_buttons_settings[max_title]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Title for Max button', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function max_goal_callback() {
        $value = isset($this->options['max_goal']) ? $this->options['max_goal'] : '';
        echo '<input type="text" id="max_goal" name="fly_buttons_settings[max_goal]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Идентификатор цели для клика по Max', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    // ------ Callback для кнопок обратной связи ------
    public function call_enabled_callback() {
        $value = isset($this->options['call_enabled']) ? $this->options['call_enabled'] : 1;
        echo '<input type="checkbox" id="call_enabled" name="fly_buttons_settings[call_enabled]" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="call_enabled">' . esc_html__('Enable callback button', 'rgbee-fly-buttons-feedback') . '</label>';
    }
    
    public function call_title_callback() {
        $value = isset($this->options['call_title']) ? $this->options['call_title'] : __('Заказать звонок', 'rgbee-fly-buttons-feedback');
        echo '<input type="text" id="call_title" name="fly_buttons_settings[call_title]" value="' . esc_attr($value) . '" class="regular-text" />';
    }
    
    public function call_link_callback() {
        $value = isset($this->options['call_link']) ? $this->options['call_link'] : '#';
        echo '<input type="text" id="call_link" name="fly_buttons_settings[call_link]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Link for callback button (can be # for modal window)', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function call_attributes_callback() {
        $value = isset($this->options['call_attributes']) ? $this->options['call_attributes'] : 'data-bs-toggle="modal" data-bs-target="#callbackModal"';
        echo '<textarea id="call_attributes" name="fly_buttons_settings[call_attributes]" class="large-text" rows="3">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . esc_html__('Custom attributes for callback button (e.g., data-bs-toggle="modal" data-bs-target="#callbackModal")', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function call_custom_class_callback() {
        $value = isset($this->options['call_custom_class']) ? $this->options['call_custom_class'] : '';
        echo '<input type="text" id="call_custom_class" name="fly_buttons_settings[call_custom_class]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Custom CSS class for callback button', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function call_goal_callback() {
        $value = isset($this->options['call_goal']) ? $this->options['call_goal'] : '';
        echo '<input type="text" id="call_goal" name="fly_buttons_settings[call_goal]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Идентификатор цели для кнопки "Заказать звонок"', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function message_enabled_callback() {
        $value = isset($this->options['message_enabled']) ? $this->options['message_enabled'] : 1;
        echo '<input type="checkbox" id="message_enabled" name="fly_buttons_settings[message_enabled]" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="message_enabled">' . esc_html__('Enable message button', 'rgbee-fly-buttons-feedback') . '</label>';
    }
    
    public function message_title_callback() {
        $value = isset($this->options['message_title']) ? $this->options['message_title'] : __('Написать сообщение', 'rgbee-fly-buttons-feedback');
        echo '<input type="text" id="message_title" name="fly_buttons_settings[message_title]" value="' . esc_attr($value) . '" class="regular-text" />';
    }
    
    public function message_link_callback() {
        $value = isset($this->options['message_link']) ? $this->options['message_link'] : '#';
        echo '<input type="text" id="message_link" name="fly_buttons_settings[message_link]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Link for message button', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function message_attributes_callback() {
        $value = isset($this->options['message_attributes']) ? $this->options['message_attributes'] : 'data-bs-toggle="modal" data-bs-target="#messageModal"';
        echo '<textarea id="message_attributes" name="fly_buttons_settings[message_attributes]" class="large-text" rows="3">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . esc_html__('Custom attributes for message button', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function message_custom_class_callback() {
        $value = isset($this->options['message_custom_class']) ? $this->options['message_custom_class'] : '';
        echo '<input type="text" id="message_custom_class" name="fly_buttons_settings[message_custom_class]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Custom CSS class for message button', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function message_goal_callback() {
        $value = isset($this->options['message_goal']) ? $this->options['message_goal'] : '';
        echo '<input type="text" id="message_goal" name="fly_buttons_settings[message_goal]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Идентификатор цели для кнопки "Написать сообщение"', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function review_enabled_callback() {
        $value = isset($this->options['review_enabled']) ? $this->options['review_enabled'] : 1;
        echo '<input type="checkbox" id="review_enabled" name="fly_buttons_settings[review_enabled]" value="1" ' . checked(1, $value, false) . ' />';
        echo '<label for="review_enabled">' . esc_html__('Enable review button', 'rgbee-fly-buttons-feedback') . '</label>';
    }
    
    public function review_title_callback() {
        $value = isset($this->options['review_title']) ? $this->options['review_title'] : __('Оставить отзыв', 'rgbee-fly-buttons-feedback');
        echo '<input type="text" id="review_title" name="fly_buttons_settings[review_title]" value="' . esc_attr($value) . '" class="regular-text" />';
    }
    
    public function review_link_callback() {
        $value = isset($this->options['review_link']) ? $this->options['review_link'] : '#';
        echo '<input type="text" id="review_link" name="fly_buttons_settings[review_link]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Link for review button', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function review_attributes_callback() {
        $value = isset($this->options['review_attributes']) ? $this->options['review_attributes'] : 'data-bs-toggle="modal" data-bs-target="#reviewModal"';
        echo '<textarea id="review_attributes" name="fly_buttons_settings[review_attributes]" class="large-text" rows="3">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . esc_html__('Custom attributes for review button', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function review_custom_class_callback() {
        $value = isset($this->options['review_custom_class']) ? $this->options['review_custom_class'] : '';
        echo '<input type="text" id="review_custom_class" name="fly_buttons_settings[review_custom_class]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Custom CSS class for review button', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    public function review_goal_callback() {
        $value = isset($this->options['review_goal']) ? $this->options['review_goal'] : '';
        echo '<input type="text" id="review_goal" name="fly_buttons_settings[review_goal]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Идентификатор цели для кнопки "Оставить отзыв"', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    // ------ Callback для Метрики ------
    public function metrika_counter_callback() {
        $value = isset($this->options['metrika_counter']) ? $this->options['metrika_counter'] : '';
        echo '<input type="text" id="metrika_counter" name="fly_buttons_settings[metrika_counter]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Введите номер вашего счётчика Яндекс.Метрики (например, 94056343)', 'rgbee-fly-buttons-feedback') . '</p>';
    }
    
    // ------ Callback для цветов ------
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
    
    // ------ Стили и скрипты ------
    public function enqueue_scripts() {
        // Проверяем существование локальных файлов Font Awesome
        $local_css_path = FLY_BUTTONS_PLUGIN_PATH . 'assets/fontawesome/css/all.min.css';
        $local_css_url = FLY_BUTTONS_PLUGIN_URL . 'assets/fontawesome/css/all.min.css';
        
        if (file_exists($local_css_path)) {
            wp_enqueue_style(
                'font-awesome',
                $local_css_url,
                array(),
                '6.2.1'
            );
        } else {
            wp_enqueue_style(
                'font-awesome',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css',
                array(),
                '6.2.1'
            );
        }
        
        $this->add_custom_styles();
    }
    
    private function add_custom_styles() {
        $options = get_option('fly_buttons_settings');
        
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
            z-index: 9999;
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

        .fly_buttons .fly_item a.max {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .fly_buttons .fly_item a.max img {
            width: 25px;
            height: 25px;
            transition: filter 0.3s ease;
        }
        .fly_buttons .fly_item a.max:hover img {
            filter: grayscale(100%) brightness(0.8);
        }
        
        .fly_buttons .fly_item a.wa {color: {$whatsapp_color};}
        .fly_buttons .fly_item a.tg {color: {$telegram_color};}
        .fly_buttons .fly_item a.vb {color: {$viber_color};}
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
    
    // ------ Вспомогательный метод для формирования onclick ------
    private function build_onclick($goal_id) {
        if (empty($this->options['metrika_counter']) || empty($goal_id)) {
            return '';
        }
        $counter = intval($this->options['metrika_counter']);
        return ' onclick="ym(' . $counter . ', \'reachGoal\', \'' . esc_js($goal_id) . '\'); return true;"';
    }
    
    // ------ Вывод кнопок на фронтенд ------
    public function display_fly_buttons() {
        $this->options = get_option('fly_buttons_settings');
        
        echo '<div class="fly_buttons">';
        
        // WhatsApp
        if (!empty($this->options['whatsapp_phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $this->options['whatsapp_phone']);
            $text = !empty($this->options['whatsapp_text']) ? urlencode($this->options['whatsapp_text']) : '';
            $title = !empty($this->options['whatsapp_title']) ? $this->options['whatsapp_title'] : __('Написать в WhatsApp', 'rgbee-fly-buttons-feedback');
            $goal = isset($this->options['whatsapp_goal']) ? $this->options['whatsapp_goal'] : '';
            $onclick = $this->build_onclick($goal);
            echo '<div class="fly_item"><a href="https://wa.me/' . esc_attr($phone) . '?text=' . esc_attr($text) . '" title="' . esc_attr($title) . '" target="_blank" class="wa"' . $onclick . '><i class="fa-brands fa-square-whatsapp"></i></a></div>';
        }
        
        // Telegram
        if (!empty($this->options['telegram_link'])) {
            $title = !empty($this->options['telegram_title']) ? $this->options['telegram_title'] : __('Написать в Telegram', 'rgbee-fly-buttons-feedback');
            $goal = isset($this->options['telegram_goal']) ? $this->options['telegram_goal'] : '';
            $onclick = $this->build_onclick($goal);
            echo '<div class="fly_item"><a href="' . esc_url($this->options['telegram_link']) . '" title="' . esc_attr($title) . '" target="_blank" class="tg"' . $onclick . '><i class="fa-brands fa-telegram"></i></a></div>';
        }
        
        // Max
        if (!empty($this->options['max_link'])) {
            $title = !empty($this->options['max_title']) ? $this->options['max_title'] : __('Написать в Max', 'rgbee-fly-buttons-feedback');
            $goal = isset($this->options['max_goal']) ? $this->options['max_goal'] : '';
            $onclick = $this->build_onclick($goal);
            echo '<div class="fly_item"><a href="' . esc_url($this->options['max_link']) . '" title="' . esc_attr($title) . '" target="_blank" class="max"' . $onclick . '><img src="' . FLY_BUTTONS_PLUGIN_URL . 'assets/icons/icon-max.png" alt="Max" style="width: 25px; height: 25px;"></a></div>';
        }
        
        // Viber
        if (!empty($this->options['viber_phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $this->options['viber_phone']);
            $title = !empty($this->options['viber_title']) ? $this->options['viber_title'] : __('Написать в Viber', 'rgbee-fly-buttons-feedback');
            $goal = isset($this->options['viber_goal']) ? $this->options['viber_goal'] : '';
            $onclick = $this->build_onclick($goal);
            echo '<div class="fly_item"><a href="viber://chat?number=+' . esc_attr($phone) . '" title="' . esc_attr($title) . '" target="_blank" class="vb"' . $onclick . '><i class="fa-brands fa-viber"></i></a></div>';
        }
        
        // Callback
        if (isset($this->options['call_enabled']) && $this->options['call_enabled']) {
            $title = !empty($this->options['call_title']) ? $this->options['call_title'] : __('Заказать звонок', 'rgbee-fly-buttons-feedback');
            $link = !empty($this->options['call_link']) ? $this->options['call_link'] : '#';
            $attributes = !empty($this->options['call_attributes']) ? $this->options['call_attributes'] : '';
            $custom_class = !empty($this->options['call_custom_class']) ? ' ' . $this->options['call_custom_class'] : '';
            $goal = isset($this->options['call_goal']) ? $this->options['call_goal'] : '';
            $onclick = $this->build_onclick($goal);
            echo '<div class="fly_item"><a href="' . esc_url($link) . '" title="' . esc_attr($title) . '" class="call-btn' . esc_attr($custom_class) . '" ' . $attributes . $onclick . '><i class="fa-solid fa-phone"></i></a></div>';
        }
        
        // Message
        if (isset($this->options['message_enabled']) && $this->options['message_enabled']) {
            $title = !empty($this->options['message_title']) ? $this->options['message_title'] : __('Написать сообщение', 'rgbee-fly-buttons-feedback');
            $link = !empty($this->options['message_link']) ? $this->options['message_link'] : '#';
            $attributes = !empty($this->options['message_attributes']) ? $this->options['message_attributes'] : '';
            $custom_class = !empty($this->options['message_custom_class']) ? ' ' . $this->options['message_custom_class'] : '';
            $goal = isset($this->options['message_goal']) ? $this->options['message_goal'] : '';
            $onclick = $this->build_onclick($goal);
            echo '<div class="fly_item"><a href="' . esc_url($link) . '" title="' . esc_attr($title) . '" class="message-btn' . esc_attr($custom_class) . '" ' . $attributes . $onclick . '><i class="fa-solid fa-envelope"></i></a></div>';
        }
        
        // Review
        if (isset($this->options['review_enabled']) && $this->options['review_enabled']) {
            $title = !empty($this->options['review_title']) ? $this->options['review_title'] : __('Оставить отзыв', 'rgbee-fly-buttons-feedback');
            $link = !empty($this->options['review_link']) ? $this->options['review_link'] : '#';
            $attributes = !empty($this->options['review_attributes']) ? $this->options['review_attributes'] : '';
            $custom_class = !empty($this->options['review_custom_class']) ? ' ' . $this->options['review_custom_class'] : '';
            $goal = isset($this->options['review_goal']) ? $this->options['review_goal'] : '';
            $onclick = $this->build_onclick($goal);
            echo '<div class="fly_item"><a href="' . esc_url($link) . '" title="' . esc_attr($title) . '" class="review-btn' . esc_attr($custom_class) . '" ' . $attributes . $onclick . '><i class="fa-solid fa-comment-dots"></i></a></div>';
        }
        
        echo '</div>';
    }
}

// Инициализация плагина
new FlyButtonsFeedback();
?>