<?php

class ThemeHouse_SocialNotices_Listener_TemplateCreate extends ThemeHouse_Listener_TemplateCreate
{

    protected function _getTemplates()
    {
        return array(
            'forum_view'
        );
    } /* END _getTemplates */

    public static function templateCreate(&$templateName, array &$params, XenForo_Template_Abstract $template)
    {
        $templateCreate = new ThemeHouse_SocialNotices_Listener_TemplateCreate($templateName, $params, $template);
        list ($templateName, $params) = $templateCreate->run();
    } /* END templateCreate */

    protected function _forumView()
    {
        $this->_preloadTemplate('th_social_forum_tools_socialnotices');
    } /* END _forumView */
}