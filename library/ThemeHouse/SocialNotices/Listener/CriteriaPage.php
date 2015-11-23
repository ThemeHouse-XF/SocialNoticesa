<?php

class ThemeHouse_SocialNotices_Listener_CriteriaPage
{

    public static function criteriaPage($rule, array $data, array $params, array $containerData, &$returnValue)
    {
        switch ($rule) {
            case 'social_forums':
                if (ThemeHouse_SocialGroups_SocialForum::hasInstance()) {
                    $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance();
                    if (isset($socialForum['social_forum_id']) &&
                         in_array($socialForum['social_forum_id'], $data['social_forum_ids'])) {
                        $returnValue = true;
                    }
                }
                break;
        }
    } /* END criteriaPage */
}