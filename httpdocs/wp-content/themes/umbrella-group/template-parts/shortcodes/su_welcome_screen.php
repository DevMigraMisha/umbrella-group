<?php

class SU_welcome_screen
{
    private $css = '<link href="/wp-content/themes/umbrella-group/css/block-su-welcome-screen.css" type="text/css" rel="stylesheet"/>';
    public $err = "<ul style='padding: 24px;'> Неправильное использование шорткода: ";

    public $atts;
    private $offer;
    private $background;
    private $icon;
    private $first_advantage = "<strong>Бесплатная</strong> консультация онлайн";
    private $second_advantage;
    private $third_advantage;
    private $button_type;
    private $button_variants = ['free_consulting', 'price_calc', 'ask_question'];
    private $phone;
    private $phone_text;
    private $specialist_text;


    public function fill_attributes()
    {
        $err_initial_len = strlen($this->err);
        $this->fill_variable('button', $this->button_type, true, 'Тип кнопки', implode('|', $this->button_variants));
        if (!in_array($this->button_type, $this->button_variants)) {
            $this->err .= "<li> Тип кнопки задан неправильно. Выберите один из вариантов кнопки: <span id='codebox'>button=" . implode('|', $this->button_variants) . "</span></li>";
        }
        $this->fill_variable('offer', $this->offer, true, 'Подзаголовок-оффер', 'Устраним все замечания к налоговой отчетности, своевременно представим корректирующие формы, восстановим первичные документы предприятия');
        $this->fill_variable('background', $this->background, true, 'Фоновое изображение', '9108');
        $this->fill_variable('icon', $this->icon, true, 'Иконка', '2252');
        $this->fill_variable('first_advantage', $this->first_advantage, false, 'Первое преимущество', '<strong>Бесплатная</strong> консультация онлайн');
        $this->fill_variable('second_advantage', $this->second_advantage, true, 'Второе преимущество', '\'Риски застрахованы на сумму более 50 млн рублей.\'');
        $this->fill_variable('third_advantage', $this->third_advantage, true, 'Третье преимущество', '\'Подадим документы за 7 дней\'');
        $this->fill_variable('phone', $this->phone, true, 'Телефон', '73832021582');
        if (!(strlen($this->phone) == 11 && intval($this->phone, 10) > 70000000000)) {
            $this->err .= "<li> Формат телефона задан неверно. Используйте следующий формат: <span id='codebox'>73832021582</span></li>";
        }
        $this->fill_variable('phone_text', $this->phone_text, true, 'Текст перед телефоном', '\'Для бесплатной консультации по услуге отправьте форму с сайта или звоните \'');
        $this->fill_variable('specialist_text', $this->specialist_text, true, 'Текст со специалистом', '\'Вам ответит наш {специалист} с опытом работы более 5 лет\'');

        if (strlen($this->err) > $err_initial_len) {
            $this->err .= '</ul>' . $this->css;
            return false;
        } else {
            return true;
        }
    }

    private function fill_variable($attribute, &$variable, $obligatory, $name, $sample_value)
    {
        if (isset($this->atts[$attribute])) {
            $variable = $this->atts[$attribute];
        } elseif ($obligatory) {
            $this->err .= <<<EOHTML
                <li> <strong>$name</strong> не задан. Используйте атрибут <span id='codebox'>$attribute</span>. Например <span id='codebox'>$attribute=$sample_value</span> </li>
                EOHTML;
        }
    }


    private function get_button()
    {
        switch ($this->button_type) {
            case 'free_consulting':
                return '[button text="Бесплатная консультация" link="#leave-request-contact-form-lightbox"]';
            case 'price_calc':
                return <<<EOHTML
                        [button text="Расчёт стоимости" link="#calculate-price-contact-form-lightbox"]
                        [lightbox id="calculate-price-contact-form-lightbox" width="400px" padding="0px"]
                            [contact-form-7 id="9965" title="Форма Расчёт стоимости услуги"]
                        [/lightbox]
                       EOHTML;
            case 'ask_question':
                return <<<EOHTML
                        [button text="Задать вопрос" link="#ask-question-contact-form-lightbox"]
                        [lightbox id="ask-question-contact-form-lightbox" width="400px" padding="0px"]
                            [contact-form-7 id="9967" title="Форма Задать вопрос"]
                        [/lightbox]
                       EOHTML;

        }


    }

    private function get_phone_text()
    {
        $formatted_phone = sprintf("+%s (%s) %s-%s-%s",
            substr($this->phone, 0, 1),
            substr($this->phone, 1, 3),
            substr($this->phone, 4, 3),
            substr($this->phone, 7, 2),
            substr($this->phone, 9));
        return <<<EOHTML
            $this->phone_text <a href="tel:$this->phone"><span class="su_welcome_banner_overlay_bottom_phone">$formatted_phone</span></a>
        EOHTML;
    }

    private function get_specialist_text()
    {
        if (strpos($_SERVER['REQUEST_URI'], "services/bukhgalterskie-uslugi/") !== false) {
            $specialist = "ведущий бухгалтер";
        } else if (strpos($_SERVER['REQUEST_URI'], "services/licensing/") !== false) {
            $specialist = "специалист по лицензированию";
        } else if (strpos($_SERVER['REQUEST_URI'], "services/services-le") !== false) {
            $specialist = "юрист";
        } else {
            $specialist = "специалист";
        }

        return str_replace('{специалист}', $specialist, $this->specialist_text);
    }

    public function generate_shortcode()
    {
        $this->icon = '<img src="' . wp_get_attachment_image_src($this->icon, 'full')[0] . '"  />';
        $this->background = '<img src="' . wp_get_attachment_image_src($this->background, 'full')[0] . '"  />';
        $breadcrumbs = umbrella_draw_header_with_breadcrumbs();
        $button = $this->get_button();
        $phone_text = $this->get_phone_text();
        $specialist_text = $this->get_specialist_text();
        $html = <<<EOHTML
        <div class="su_welcome_screen">
            <div class="su_welcome_banner">
                    <div class="su_welcome_banner_content dark">
                        $breadcrumbs
                        <div class="su_welcome_banner_offer container">
                            <p>$this->offer</p> 
                        </div>      
                    </div>
                    <div class="su_welcome_banner_background">
                        $this->background
                    </div>
            </div>
            <div class="su_welcome_banner_overlay">
                  <div class="show-for-medium">$button</div>
                    <div class="su_welcome_banner_overlay_top">
                        <div class="advantage-block"> 
                            <div class="advantage-block-icon">$this->icon</div> 
                            <div class="advantage-block-text">$this->first_advantage</div>
                        </div>
                        <div class="advantage-block"> 
                            <div class="advantage-block-icon">$this->icon</div> 
                            <div class="advantage-block-text">$this->second_advantage</div>
                        </div>
                        <div class="advantage-block"> 
                            <div class="advantage-block-icon">$this->icon</div> 
                            <div class="advantage-block-text">$this->third_advantage</div>
                        </div>
                    </div>
                    <div class="su_welcome_banner_overlay_bottom">
                        <p>$phone_text</p>
                        <p>$specialist_text</p>
                        <div class="hide-for-medium">$button</div>
                    </div>
            </div>
        </div>
        EOHTML;
        $html .= $this->css;
        return $html;
    }

}

function su_welcome_screen_shortcode($atts)
{
    $shortcode = new SU_welcome_screen();
    $shortcode->atts = $atts;
    if (!$shortcode->fill_attributes()) {
        return $shortcode->err;
    }
    return $shortcode->generate_shortcode();
}

add_shortcode('su_welcome_screen', 'su_welcome_screen_shortcode');


