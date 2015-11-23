<?php

class ThemeHouse_SocialNotices_Listener_TemplatePostRender extends ThemeHouse_Listener_TemplatePostRender
{

    protected function _getTemplates()
    {
        return array(
            'notice_edit'
        );
    } /* END _getTemplates */

    public static function templatePostRender($templateName, &$content, array &$containerData,
        XenForo_Template_Abstract $template)
    {
        $templatePostRender = new ThemeHouse_SocialNotices_Listener_TemplatePostRender($templateName, $content,
            $containerData, $template);
        list ($content, $containerData) = $templatePostRender->run();
    } /* END templatePostRender */

    protected function _noticeEdit()
    {
        $pattern = '#<dl class="ctrlUnit">\s*<dt>' . new XenForo_Phrase('nodes') . ':</dt>\s*<dd>.*</dd>\s*</dl>#Us';
        $replacement = '$0' . $this->_render('th_helper_criteria_page_socialnotices');
        $this->_contents = preg_replace($pattern, $replacement, $this->_contents);
    } /* END _noticeEdit */
}