<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use App\Service\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccessController extends AbstractController
{
    private const ADMIN_EMAIL = 'hamz57914@gmail.com';
    private const DEFAULT_PASSWORD = 'united';

    #[Route('/request-access', name: 'request_access')]
    public function requestAccess(): Response
    {
        return $this->render('access/request.html.twig');
    }

    #[Route('/verify-email', name: 'verify_email', methods: ['POST'])]
    public function verifyEmail(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $email = $request->request->get('email');
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['valid' => false, 'message' => 'Format d\'email invalide']);
        }
        
        $blockedDomains = ['tempmail.com', 'throwaway.email', '10minutemail.com', 'guerrillamail.com', 'mailinator.com'];
        $domain = strtolower(explode('@', $email)[1] ?? '');
        
        if (in_array($domain, $blockedDomains)) {
            return $this->json(['valid' => false, 'message' => 'Les emails temporaires ne sont pas acceptés']);
        }
        
        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return $this->json(['valid' => false, 'message' => 'Cet email a déjà un compte']);
        }
        
        return $this->json(['valid' => true, 'message' => 'Email valide']);
    }

    #[Route('/send-access-request', name: 'send_access_request', methods: ['POST'])]
    public function sendAccessRequest(Request $request, Mail $mail): JsonResponse
    {
        error_log('=== START send-access-request ===');
        
        try {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $role = $request->request->get('role');
            $message = $request->request->get('message', '');

            error_log("Received: name=$name, email=$email, role=$role");

            if (!$name || !$email || !$role) {
                error_log('Missing fields');
                return $this->json([
                    'success' => false,
                    'message' => 'Tous les champs sont requis'
                ], 400);
            }

            // Générer le token et les données pour la validation
            $validationToken = bin2hex(random_bytes(32));
            $requestData = base64_encode(json_encode([
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'message' => $message,
                'token' => $validationToken,
                'timestamp' => time()
            ]));

            error_log('Generating validation URL...');
            
            // URL de validation automatique - data en query parameter
            $validateUrl = $this->generateUrl('validate_access_request', [
                'token' => $validationToken
            ], UrlGeneratorInterface::ABSOLUTE_URL) . '?data=' . urlencode($requestData);

            error_log('Validation URL: ' . $validateUrl);

            // HTML simplifié pour tester
            $htmlContent = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
</head>
<body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
    <div style='background: #DA291C; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
        <h1 style='margin: 0;'>Manchester United</h1>
        <p style='margin: 10px 0 0;'>Nouvelle Demande d'Accès</p>
    </div>
    
    <div style='background: white; padding: 30px; border: 1px solid #ddd; border-radius: 0 0 10px 10px;'>
        <h2>Détails de la demande</h2>
        
        <table style='width: 100%; border-collapse: collapse;'>
            <tr style='border-bottom: 1px solid #eee;'>
                <td style='padding: 10px; font-weight: bold;'>Nom :</td>
                <td style='padding: 10px;'>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</td>
            </tr>
            <tr style='border-bottom: 1px solid #eee;'>
                <td style='padding: 10px; font-weight: bold;'>Email :</td>
                <td style='padding: 10px;'>" . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</td>
            </tr>
            <tr style='border-bottom: 1px solid #eee;'>
                <td style='padding: 10px; font-weight: bold;'>Fonction :</td>
                <td style='padding: 10px;'>" . htmlspecialchars($role, ENT_QUOTES, 'UTF-8') . "</td>
            </tr>
            " . ($message ? "
            <tr>
                <td style='padding: 10px; font-weight: bold;'>Message :</td>
                <td style='padding: 10px;'>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</td>
            </tr>
            " : "") . "
        </table>
        
        <div style='text-align: center; margin: 30px 0;'>
            <a href='" . htmlspecialchars($validateUrl, ENT_QUOTES, 'UTF-8') . "' 
               style='display: inline-block; padding: 15px 40px; background: #00B050; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                VALIDER ET CRÉER LE COMPTE
            </a>
        </div>
        
        <div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>
            <p style='margin: 0;'><strong>Informations :</strong></p>
            <ul style='margin: 10px 0;'>
                <li>Mot de passe par défaut : <strong>" . self::DEFAULT_PASSWORD . "</strong></li>
                <li>Lien valide 24 heures</li>
            </ul>
        </div>
    </div>
    
    <div style='text-align: center; padding: 20px; color: #999; font-size: 12px;'>
        <p>Manchester United F.C. - Dashboard Administration</p>
    </div>
</body>
</html>";

            error_log('HTML created, length: ' . strlen($htmlContent));
            error_log('Calling mail service...');

            $fromEmail = 'hamz57914@gmail.com';
            $subject = '[MU Dashboard] Nouvelle demande d\'accès de ' . $name;
            
            $success = $mail->send($fromEmail, self::ADMIN_EMAIL, $subject, $htmlContent);

            error_log('Mail service returned: ' . ($success ? 'true' : 'false'));

            if ($success) {
                return $this->json([
                    'success' => true,
                    'message' => '✅ Demande envoyée avec succès ! L\'administrateur recevra une notification.'
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => '❌ Erreur lors de l\'envoi. Veuillez réessayer.'
                ], 500);
            }
        } catch (\Exception $e) {
            error_log('EXCEPTION in sendAccessRequest: ' . $e->getMessage());
            error_log('File: ' . $e->getFile() . ':' . $e->getLine());
            error_log('Trace: ' . $e->getTraceAsString());
            
            return $this->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/validate-access/{token}', name: 'validate_access_request', methods: ['GET'])]
    public function validateAccessRequest(
        string $token,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        Mail $mail
    ): Response {
        // Récupérer data depuis les query parameters au lieu de l'URL
        $data = $request->query->get('data');
        
        if (!$data) {
            $this->addFlash('error', '❌ Données manquantes');
            return $this->redirectToRoute('app_login');
        }
        error_log('=== VALIDATE ACCESS REQUEST ===');
        error_log('Token: ' . $token);
        error_log('Data length: ' . strlen($data));
        
        try {
            $requestData = json_decode(base64_decode($data), true);
            
            if (!$requestData) {
                throw new \Exception('Données invalides');
            }

            error_log('Decoded data: ' . print_r($requestData, true));

            if (!isset($requestData['token']) || $requestData['token'] !== $token) {
                throw new \Exception('Token invalide');
            }

            if (isset($requestData['timestamp']) && (time() - $requestData['timestamp']) > 86400) {
                throw new \Exception('Lien expiré (plus de 24h)');
            }

            $name = $requestData['name'];
            $email = $requestData['email'];
            $role = $requestData['role'];

            error_log("Creating user: $email");

            $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            
            if ($existingUser) {
                error_log('User already exists');
                $this->addFlash('warning', '⚠️ Ce compte existe déjà !');
                return $this->redirectToRoute('app_login');
            }

            // Créer le nouveau compte
            $user = new User();
            $user->setEmail($email);
            $hashedPassword = $passwordHasher->hashPassword($user, self::DEFAULT_PASSWORD);
            $user->setPassword($hashedPassword);

            // Générer le token d'accès
            $accessToken = bin2hex(random_bytes(32));
            $user->setAccessToken($accessToken);
            $user->setAccessTokenExpiresAt(new \DateTime('+24 hours'));

            $em->persist($user);
            $em->flush();

            error_log('✓ User created successfully');

            $accessUrl = $this->generateUrl('access_login', ['token' => $accessToken], UrlGeneratorInterface::ABSOLUTE_URL);

            error_log('Access URL: ' . $accessUrl);
            error_log('Sending email to coach...');

            // Email simplifié au coach
            $htmlContentCoach = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
</head>
<body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5;'>
    <div style='background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
        <!-- Header -->
        <div style='background: linear-gradient(135deg, #DA291C 0%, #B71C1C 100%); color: white; padding: 40px; text-align: center;'>
            <div style='width: 80px; height: 80px; background: white; border-radius: 50%; display: inline-block; line-height: 80px; font-size: 2rem; font-weight: bold; color: #DA291C; margin-bottom: 20px;'>MU</div>
            <h1 style='margin: 0; font-size: 1.5rem;'>🔔 Nouvelle Demande d'Accès</h1>
            <p style='margin: 10px 0 0; opacity: 0.9;'>Dashboard Manchester United</p>
        </div>
        
        <!-- Content -->
        <div style='padding: 40px;'>
            <h2 style='color: #333; margin: 0 0 20px;'>Détails de la demande</h2>
            
            <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                <tr style='border-bottom: 1px solid #eee;'>
                    <td style='padding: 12px 0; font-weight: bold; color: #DA291C; width: 120px;'>👤 Nom :</td>
                    <td style='padding: 12px 0; color: #333;'>" . htmlspecialchars($name) . "</td>
                </tr>
                <tr style='border-bottom: 1px solid #eee;'>
                    <td style='padding: 12px 0; font-weight: bold; color: #DA291C;'>📧 Email :</td>
                    <td style='padding: 12px 0; color: #333;'>" . htmlspecialchars($email) . "</td>
                </tr>
                <tr style='border-bottom: 1px solid #eee;'>
                    <td style='padding: 12px 0; font-weight: bold; color: #DA291C;'>💼 Fonction :</td>
                    <td style='padding: 12px 0; color: #333;'>" . htmlspecialchars($role) . "</td>
                </tr>
                " . ($message ? "
                <tr>
                    <td style='padding: 12px 0; font-weight: bold; color: #DA291C; vertical-align: top;'>💬 Message :</td>
                    <td style='padding: 12px 0; color: #333;'>" . nl2br(htmlspecialchars($message)) . "</td>
                </tr>
                " : "") . "
            </table>
            
            <!-- Action Box -->
            <div style='background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border: 2px solid #4caf50; padding: 30px; margin: 30px 0; border-radius: 10px; text-align: center;'>
                <h3 style='color: #2e7d32; margin: 0 0 15px; font-size: 1.3rem;'>✅ Validation en 1 clic !</h3>
                <p style='color: #555; margin: 0 0 25px; font-size: 0.95rem;'>Cliquez sur le bouton ci-dessous pour :</p>
                <ul style='text-align: left; color: #555; margin: 0 0 25px; padding-left: 40px;'>
                    <li>✓ Créer automatiquement le compte</li>
                    <li>✓ Générer le mot de passe : <strong>united</strong></li>
                    <li>✓ Envoyer les identifiants au coach</li>
                </ul>
                <a href='" . $validateUrl . "' 
                   style='display: inline-block; padding: 18px 50px; background: #4caf50; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4); transition: all 0.3s;'>
                    ✓ VALIDER ET CRÉER LE COMPTE
                </a>
            </div>

            <!-- Info Box -->
            <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 5px;'>
                <p style='margin: 0 0 10px; font-weight: bold; color: #856404;'>ℹ️ Informations :</p>
                <ul style='margin: 0; padding-left: 20px; color: #856404;'>
                    <li>Mot de passe par défaut : <strong>" . self::DEFAULT_PASSWORD . "</strong></li>
                    <li>Le coach recevra un email automatiquement</li>
                    <li>Lien d'accès valide 24 heures</li>
                </ul>
            </div>
        </div>
        
        <!-- Footer -->
        <div style='background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;'>
            <p style='margin: 0; font-weight: bold; color: #333;'>Manchester United F.C.</p>
            <p style='margin: 5px 0 0; color: #6c757d; font-size: 0.85rem;'>Dashboard Administration - Performance Analytics</p>
        </div>
    </div>
</body>
</html>";

            $emailSent = $mail->send('hamz57914@gmail.com', $email, 'Bienvenue à Manchester United - Vos Identifiants', $htmlContentCoach);

            error_log('Email sent to coach: ' . ($emailSent ? 'true' : 'false'));

            // Afficher page de confirmation
            return $this->render('access/validation_success.html.twig', [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'password' => self::DEFAULT_PASSWORD
            ]);

        } catch (\Exception $e) {
            error_log('EXCEPTION in validateAccessRequest: ' . $e->getMessage());
            error_log('File: ' . $e->getFile() . ':' . $e->getLine());
            
            $this->addFlash('error', '❌ Erreur : ' . $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/send-access', name: 'send_access', methods: ['POST'])]
    public function sendAccess(
        Request $request, 
        EntityManagerInterface $em, 
        Mail $mail
    ): JsonResponse {
        $email = $request->request->get('email');

        if (!$email) {
            return $this->json(['success' => false, 'message' => 'Email requis'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Utilisateur non trouvé'], 404);
        }

        $token = bin2hex(random_bytes(32));
        $user->setAccessToken($token);
        $user->setAccessTokenExpiresAt(new \DateTime('+15 minutes'));
        $em->flush();

        $url = $this->generateUrl('access_login', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        $htmlContent = "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'></head>
<body style='font-family: Arial; max-width: 600px; margin: 0 auto; padding: 20px;'>
    <h1 style='color: #DA291C;'>Lien d'accès Dashboard</h1>
    <p>Cliquez pour accéder :</p>
    <div style='text-align: center; margin: 30px 0;'>
        <a href='" . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . "' 
           style='display: inline-block; padding: 15px 40px; background: #DA291C; color: white; text-decoration: none; border-radius: 5px;'>
            ACCÉDER
        </a>
    </div>
    <p style='color: #999; font-size: 12px;'>Valide 15 minutes</p>
</body>
</html>";

        $success = $mail->send('hamz57914@gmail.com', $user->getEmail(), 'MU Dashboard - Lien d\'accès', $htmlContent);

        return $this->json([
            'success' => $success,
            'message' => $success ? '✅ Email envoyé !' : '❌ Erreur'
        ]);
    }

    #[Route('/access-login/{token}', name: 'access_login')]
    public function accessLogin(
        string $token,
        EntityManagerInterface $em,
        UserAuthenticatorInterface $authenticator,
        LoginFormAuthenticator $formAuthenticator,
        Request $request
    ): Response {
        $user = $em->getRepository(User::class)->findOneBy(['accessToken' => $token]);

        if (!$user) {
            $this->addFlash('error', '❌ Token invalide ou expiré');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getAccessTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('error', '⏱️ Token expiré');
            return $this->redirectToRoute('app_login');
        }

        $user->setAccessToken(null);
        $user->setAccessTokenExpiresAt(null);
        $em->flush();

        return $authenticator->authenticateUser($user, $formAuthenticator, $request);
    }
}