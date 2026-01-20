<?php
namespace App\Config;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class Twig
{
    private static ?Environment $twig = null;

    public static function getInstance(): Environment
    {
        if (self::$twig === null) {
            $loader = new FilesystemLoader(__DIR__ . '/../Views');
            
            self::$twig = new Environment($loader, [
                'cache' => false, // Set to __DIR__ . '/../../cache' in production
                'debug' => true,
                'auto_reload' => true
            ]);

            // Add global variables
            self::$twig->addGlobal('session', $_SESSION);
            
            // Add url() function
            self::$twig->addFunction(new TwigFunction('url', function ($path) {
                // Remove any leading slash and add it back for consistency
                $path = '/' . ltrim($path, '/');
                
                // Get base path if needed (adjust based on your setup)
                $basePath = '';
                if (strpos($_SERVER['REQUEST_URI'], '/Talent_Hub/public') !== false) {
                    $basePath = '/Talent_Hub/public';
                }
                
                return $basePath . $path;
            }));
            
            // Add asset() function for CSS/JS files
            self::$twig->addFunction(new TwigFunction('asset', function ($path) {
                $basePath = '';
                if (strpos($_SERVER['REQUEST_URI'], '/Talent_Hub/public') !== false) {
                    $basePath = '/Talent_Hub/public';
                }
                
                return $basePath . '/' . ltrim($path, '/');
            }));
        }

        return self::$twig;
    }

    public static function render(string $template, array $data = []): void
    {
        echo self::getInstance()->render($template, $data);
    }
}