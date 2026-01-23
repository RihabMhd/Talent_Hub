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
            // n loadew views mn dossier Views/
            $loader = new FilesystemLoader(__DIR__ . '/../Views');
            
            // n configurew twig environment
            self::$twig = new Environment($loader, [
                'cache' => false, 
                'debug' => true,
                'auto_reload' => true
            ]);

            // nzidou session k global variable bach nst3mloh f templates
            self::$twig->addGlobal('session', $_SESSION);
            
            // function url() bach ngenerrew urls f templates
            self::$twig->addFunction(new TwigFunction('url', function ($path) {
                // n cleanew path w nrja3ouh consistent
                $path = '/' . ltrim($path, '/');
                
                // n checkew ila khassna base path (local dev setup)
                $basePath = '';
                if (strpos($_SERVER['REQUEST_URI'], '/Talent_Hub/public') !== false) {
                    $basePath = '/Talent_Hub/public';
                }
                
                return $basePath . $path;
            }));
            
            // function asset() dial css/js/images files
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
        // n renderew template w n echo resultat
        echo self::getInstance()->render($template, $data);
    }
}