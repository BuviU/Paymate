<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7521b415ea69d204757dd9a67e5dc093
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Hub\\Traits\\Time' => __DIR__ . '/../..' . '/src/traits/time.php',
        'LearnDash\\Hub\\Boot' => __DIR__ . '/../..' . '/src/boot.php',
        'LearnDash\\Hub\\Component\\API' => __DIR__ . '/../..' . '/src/component/class-api.php',
        'LearnDash\\Hub\\Component\\Projects' => __DIR__ . '/../..' . '/src/component/class-projects.php',
        'LearnDash\\Hub\\Controller\\Licensing_Settings' => __DIR__ . '/../..' . '/src/controller/class-licensing-settings-page.php',
        'LearnDash\\Hub\\Controller\\Licensing_Settings_Section' => __DIR__ . '/../..' . '/src/controller/class-licensing-settings-section.php',
        'LearnDash\\Hub\\Controller\\Main_Controller' => __DIR__ . '/../..' . '/src/controller/class-main-controller.php',
        'LearnDash\\Hub\\Controller\\Projects_Controller' => __DIR__ . '/../..' . '/src/controller/class-projects-controller.php',
        'LearnDash\\Hub\\Controller\\Settings_Controller' => __DIR__ . '/../..' . '/src/controller/class-settings-controller.php',
        'LearnDash\\Hub\\Controller\\Signin_Controller' => __DIR__ . '/../..' . '/src/controller/class-signin-controller.php',
        'LearnDash\\Hub\\Framework\\Base' => __DIR__ . '/../..' . '/framework/class-base.php',
        'LearnDash\\Hub\\Framework\\Controller' => __DIR__ . '/../..' . '/framework/class-controller.php',
        'LearnDash\\Hub\\Framework\\DB_Builder' => __DIR__ . '/../..' . '/framework/class-db-builder.php',
        'LearnDash\\Hub\\Framework\\Error' => __DIR__ . '/../..' . '/framework/error.php',
        'LearnDash\\Hub\\Framework\\File' => __DIR__ . '/../..' . '/framework/file.php',
        'LearnDash\\Hub\\Framework\\Mapper' => __DIR__ . '/../..' . '/framework/class-abstract-mapper.php',
        'LearnDash\\Hub\\Framework\\Model' => __DIR__ . '/../..' . '/framework/class-model.php',
        'LearnDash\\Hub\\Framework\\View' => __DIR__ . '/../..' . '/framework/class-view.php',
        'LearnDash\\Hub\\Model\\Project' => __DIR__ . '/../..' . '/src/model/class-project.php',
        'LearnDash\\Hub\\Traits\\Formats' => __DIR__ . '/../..' . '/src/traits/formats.php',
        'LearnDash\\Hub\\Traits\\License' => __DIR__ . '/../..' . '/src/traits/license.php',
        'LearnDash\\Hub\\Traits\\Permission' => __DIR__ . '/../..' . '/src/traits/permission.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit7521b415ea69d204757dd9a67e5dc093::$classMap;

        }, null, ClassLoader::class);
    }
}
