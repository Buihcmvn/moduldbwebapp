<?php
// src/Security/Voter/MyServiceVoter.php
declare(strict_types=1);

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Service\FileService; // Import service

class MyServiceVoter extends Voter
{
    // Quyền mà Voter này sẽ kiểm tra
    public const ACCESS = 'SERVICE_ACCESS';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // Chỉ hỗ trợ quyền ACCESS và subject là một instance của FileService
        return $attribute === self::ACCESS && $subject instanceof FileService;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        // Kiểm tra quy tắc truy cập từ ROLE
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($this->security->isGranted('ROLE_ENTWICKLER')) {
            // Có thể thêm kiểm tra điều kiện bổ sung tại đây
            return true;
        }

        return false; // Từ chối truy cập
    }
}