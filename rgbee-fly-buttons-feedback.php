<?php
/**
 * Plugin Name: RGBee Fly Buttons Feedback
 * Description: Плавающие кнопки обратной связи для WhatsApp, Telegram, Viber и форм обратной связи
 * Version:     1.0.0
 * Author: Александр Курков
 * Author URI: https://rgbee.ru
 */

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

// Константы плагина
define('FLY_BUTTONS_VERSION', '1.0.0');
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
        
        add_settings_section(
            'fly_buttons_social_section',
            __('Social Media Settings', 'fly-buttons-feedback'),
            array($this, 'social_section_callback'),
            'fly_buttons_admin'
        );
        
        add_settings_section(
            'fly_buttons_style_section',
            __('Style Settings', 'fly-buttons-feedback'),
            array($this, 'style_section_callback'),
            'fly_buttons_admin'
        );
        
        // Поля для социальных сетей
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
        
        // Поля для стилей
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
        
        // Санитизация полей
        $sanitized_input['whatsapp_phone'] = sanitize_text_field($input['whatsapp_phone']);
        $sanitized_input['whatsapp_text'] = sanitize_text_field($input['whatsapp_text']);
        $sanitized_input['telegram_link'] = esc_url_raw($input['telegram_link']);
        $sanitized_input['telegram_title'] = sanitize_text_field($input['telegram_title']);
        $sanitized_input['viber_phone'] = sanitize_text_field($input['viber_phone']);
        
        // Санитизация цветов
        $sanitized_input['default_color'] = sanitize_hex_color($input['default_color']);
        $sanitized_input['hover_color'] = sanitize_hex_color($input['hover_color']);
        $sanitized_input['whatsapp_color'] = sanitize_hex_color($input['whatsapp_color']);
        $sanitized_input['telegram_color'] = sanitize_hex_color($input['telegram_color']);
        $sanitized_input['viber_color'] = sanitize_hex_color($input['viber_color']);
        
        return $sanitized_input;
    }
    
    // Callback функции для полей
    public function social_section_callback() {
        echo '<p>' . esc_html__('Configure your social media contacts and messaging settings.', 'fly-buttons-feedback') . '</p>';
    }
    
    public function style_section_callback() {
        echo '<p>' . esc_html__('Customize the appearance of the fly buttons.', 'fly-buttons-feedback') . '</p>';
    }
    
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
    
    // Callback функции для цветов
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
        
        $default_color = isset($options['default_color']) ? $options['default_color'] : '#aaaaaa';
        $hover_color = isset($options['hover_color']) ? $options['hover_color'] : '#1a1a1a';
        $whatsapp_color = isset($options['whatsapp_color']) ? $options['whatsapp_color'] : '#2ab13f';
        $telegram_color = isset($options['telegram_color']) ? $options['telegram_color'] : '#25a3e2';
        $viber_color = isset($options['viber_color']) ? $options['viber_color'] : '#7a4f99';
        
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
        .fly_buttons .fly_item a.wa {color: {$whatsapp_color};}
        .fly_buttons .fly_item a.tg {color: {$telegram_color};}
        .fly_buttons .fly_item a.vb {color: {$viber_color};}
        .fly_buttons .fly_item a.vb:hover,
        .fly_buttons .fly_item a.wa:hover,
        .fly_buttons .fly_item a.tg:hover {color: {$hover_color};}

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
        
        // Формы обратной связи (заглушки)
        echo '<div class="fly_item"><a href="#" title="' . esc_attr__('Заказать звонок', 'fly-buttons-feedback') . '"><i class="fa-solid fa-phone"></i></a></div>';
        echo '<div class="fly_item"><a href="#" title="' . esc_attr__('Написать сообщение', 'fly-buttons-feedback') . '"><i class="fa-solid fa-envelope"></i></a></div>';
        echo '<div class="fly_item"><a href="#" title="' . esc_attr__('Оставить отзыв', 'fly-buttons-feedback') . '"><i class="fa-solid fa-comment-dots"></i></a></div>';
        
        echo '</div>';
    }
}

// Инициализация плагина
new FlyButtonsFeedback();
?>