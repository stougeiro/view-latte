<?php declare(strict_types=1);

    namespace STDW\View\Latte;

    defined('TEMP') or define('TEMP', sys_get_temp_dir());

    use STDW\Contract\ServiceProviderAbstracted;
    use STDW\View\Contract\ViewInterface;
    use STDW\View\Contract\ViewHandlerInterface;
    use STDW\View\View;


    class ServiceProvider extends ServiceProviderAbstracted
    {
        public function register(): void
        {
            $this->app->singleton(ViewInterface::class, View::class);
            $this->app->singleton(ViewHandlerInterface::class, function() {
                $handler = new ViewLatteHandler();
                $handler->setTempDirectory(TEMP);

                return $handler;
            });

            $this->app::macro('view', function() {
                return $this->app->make(ViewInterface::class);
            });
        }

        public function boot(): void
        {
        }

        public function terminate(): void
        {
        }
    }