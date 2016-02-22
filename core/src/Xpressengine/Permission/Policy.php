<?php
/**
 * This file is the abstract policy for permission.
 *
 * PHP version 5
 *
 * @category    Permission
 * @package     Xpressengine\Permission
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Permission;

use Xpressengine\Member\Entities\Guest;
use Xpressengine\Member\Entities\MemberEntityInterface;
use Xpressengine\Member\Rating;
use Xpressengine\Member\Repositories\VirtualGroupRepositoryInterface;

/**
 * Abstract class Policy
 *
 * @category    Permission
 * @package     Xpressengine\Permission
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
abstract class Policy
{
    /**
     * PermissionHandler instance
     *
     * @var PermissionHandler
     */
    protected $perm;

    /**
     * VirtualGroupRepository instance
     *
     * @var VirtualGroupRepositoryInterface
     */
    protected $vgroups;

    /**
     * Policy constructor.
     * @param PermissionHandler               $perm    PermissionHandler instance
     * @param VirtualGroupRepositoryInterface $vgroups VirtualGroupRepository instance
     */
    public function __construct(PermissionHandler $perm, VirtualGroupRepositoryInterface $vgroups)
    {
        $this->perm = $perm;
        $this->vgroups = $vgroups;
    }

    /**
     * Get a permission
     *
     * @param string $name    permission name
     * @param string $siteKey site key name
     * @return Permission|null
     */
    protected function get($name, $siteKey = 'default')
    {
        return $this->perm->get($name, $siteKey);
    }

    /**
     * Check allows
     *
     * @param MemberEntityInterface $user       user instance
     * @param Permission            $permission permission instance
     * @param string                $action     action keyword
     * @return bool
     */
    protected function check(MemberEntityInterface $user, Permission $permission, $action)
    {
        $grants = $permission[$action] ?: [];

        // 제외대상 정보를 추출하면서 전체 정보에서 제외대상 정보를 제거함
        $excepts = $this->extractExcepts($grants);

        // 제외대상 회원 우선 처리
        if ($this->isExcepted($user, $excepts) === true) {
            return false;
        }

        $result = false;
        foreach ($grants as $type => $value) {
            if ($this->checker($user, $type, $value)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Extract except user information
     *
     * @param array $grants all grants information
     * @return array user identifiers
     */
    protected function extractExcepts(array &$grants)
    {
        $excepts = isset($grants[Grant::EXCEPT_TYPE]) ? $grants[Grant::EXCEPT_TYPE] : [];

        if (!empty($excepts) || count($excepts) == 0) {
            unset($grants[Grant::EXCEPT_TYPE]);
        }

        return $excepts;
    }

    /**
     * Check except user
     *
     * @param MemberEntityInterface $user    user instance
     * @param array                 $userIds except target identifiers
     * @return bool
     */
    protected function isExcepted($user, array $userIds = [])
    {
        if (empty($userIds) === true || $this->isGuest($user)) {
            return false;
        }

        return in_array($user->getId(), $userIds) ?: false;
    }

    /**
     * 타입에 맞는 권한 판별 메서드를 호출 함.
     *
     * @param MemberEntityInterface $user  user instance
     * @param string                $type  check type
     * @param mixed                 $value given value
     * @return bool
     */
    protected function checker($user, $type, $value)
    {
        $inspectMethod = $type . 'Inspect';

        return $this->$inspectMethod($user, $value);
    }

    /**
     * User 가 속한 그룹이 권한이 있는지 판별.
     *
     * @param MemberEntityInterface $user      user instance
     * @param array                 $criterion criterion group ids
     * @return bool
     */
    protected function groupInspect($user, $criterion)
    {
        $groups = $user->getGroups();
        foreach ($groups as $group) {
            if (in_array($group->id, $criterion)) {
                return true;
            }
        }

        return false;
    }

    /**
     * User 가 속한 가상그룹이 권한이 있는지 판별.
     *
     * @param MemberEntityInterface $user      user instance
     * @param array                 $criterion criterion vgroup ids
     * @return bool
     */
    protected function vgroupInspect($user, $criterion)
    {
        if ($this->isGuest($user)) {
            return false;
        }

        $groups = $this->vgroups->fetchAllByMember($user->getId());
        $groups = array_map(function ($group) {
            return $group->id;
        }, $groups);

        $intersect = array_intersect($groups, $criterion);

        return !empty($intersect);
    }

    /**
     * User 가 권한이 있는 대상으로 지정되어 있는지 판별
     *
     * @param MemberEntityInterface $user      user instance
     * @param array                 $criterion criterion user ids
     * @return bool
     */
    protected function userInspect($user, $criterion)
    {
        if (!$this->isGuest($user) && in_array($user->getId(), $criterion)) {
            return true;
        }

        return false;
    }

    /**
     * User 가 권한이 있는 등급인지 판별
     *
     * @param MemberEntityInterface $user      user instance
     * @param string                $criterion user rating keyword
     * @return bool
     */
    protected function ratingInspect($user, $criterion)
    {
        if (Rating::compare($this->userRating($user), $criterion) == -1) {
            return false;
        }

        return true;
    }

    /**
     * Get a User's rating keyword
     *
     * @param MemberEntityInterface $user user instance
     * @return string
     */
    protected function userRating($user)
    {
        if ($this->isGuest($user) === true) {
            return Rating::GUEST;
        }

        return $user->getRating();
    }

    /**
     * 전달된 사용자가 guest 인지 확인
     *
     * @param MemberEntityInterface $user user instance
     * @return bool
     */
    protected function isGuest($user)
    {
        return $user instanceof Guest;
    }
}
