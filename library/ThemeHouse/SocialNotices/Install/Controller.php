<?php

class ThemeHouse_SocialNotices_Install_Controller extends ThemeHouse_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/social-notices.1877/';

    /**
     *
     * @see ThemeHouse_Install::_getPrerequisites()
     */
    protected function _getPrerequisites()
    {
        return array(
            'ThemeHouse_SocialGroups' => '1374286183'
        );
    } /* END _getPrerequisites */

    /**
     *
     * @see ThemeHouse_Install::_getTables()
     */
    protected function _getTables()
    {
        return array(
            'xf_social_notice' => array(
                'social_forum_id' => 'INT(11) UNSIGNED NOT NULL', /* 'social_forum_id' */
                'notice_id' => 'INT(11) UNSIGNED NOT NULL', /* 'notice_id' */
            ), /* 'xf_social_notice' */
        );
    } /* END _getTables */

    /**
     *
     * @see ThemeHouse_Install::_getUniqueKeys()
     */
    protected function _getUniqueKeys()
    {
        return array(
            'xf_social_notice' => array(
                'socialForumId_noticeId' => array(
                    'social_forum_id',
                    'notice_id'
                ), /* 'socialForumId_noticeId' */
            ), /* 'xf_social_notice' */
        );
    } /* END _getUniqueKeys */

    /**
     *
     * @see ThemeHouse_Install::_getPermissionEntries()
     */
    protected function _getPermissionEntries()
    {
        return array(
            'forum' => array(
                'manageSocialNotices' => array(
                    'permission_group_id' => 'forum', /* 'permission_group_id' */
                    'permission_id' => 'editSocialForum', /* 'permission_id' */
                ), /* 'manageSocialNotices' */
            ), /* 'forum' */
        );
    } /* END _getPermissionEntries */
}