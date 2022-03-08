<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController extends AbstractController {
    /**
     * Disconnect a user
     * @return Void
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): Void {}
}
