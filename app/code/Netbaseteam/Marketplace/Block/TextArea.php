<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block;

class TextArea extends \Magento\Config\Block\System\Config\Form\Field {

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script>
            require([
                        "jquery",
                        "mage/adminhtml/wysiwyg/tiny_mce/setup"
                    ], function (jQuery) {

                        var config = "",
                            editor;

                        jQuery.extend(config, {
                            settings: {
                                theme_advanced_buttons1: "bold,italic,|,justifyleft,justifycenter,justifyright,|," +
                                "fontselect,fontsizeselect,|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code",
                                theme_advanced_buttons2: null,
                                theme_advanced_buttons3: null,
                                theme_advanced_buttons4: null,
                                theme_advanced_statusbar_location: null
                            },
                            files_browser_window_url: false
                        });

                        editor = new tinyMceWysiwygSetup(
                            "netbaseteammp_account_term_and_condition",
                            config
                        );

                        editor.turnOn();

                        jQuery("#netbaseteammp_account_term_and_condition")
                            .addClass("wysiwyg-editor")
                            .data(
                                "wysiwygEditor",
                                editor
                            );
                    });
            </script>';
        return $html;
    }

}