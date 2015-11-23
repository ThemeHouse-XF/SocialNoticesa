<?php

class ThemeHouse_SocialNotices_Listener_TemplateHook extends ThemeHouse_Listener_TemplateHook
{

    protected function _getHooks()
    {
        return array(
            'th_social_forum_tools_socialgroups'
        );
    } /* END _getHooks */

    public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
    {
        $templateHook = new ThemeHouse_SocialNotices_Listener_TemplateHook($hookName, $contents, $hookParams, $template);
        $contents = $templateHook->run();
    } /* END templateHook */

    protected function _thSocialForumToolsSocialgroups()
    {
        $this->_appendTemplate('th_social_forum_tools_socialnotices');
    } /* END _thSocialForumToolsSocialgroups */
}