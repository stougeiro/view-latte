<?php declare(strict_types=1);

    namespace STDW\View\Latte;

    use STDW\Contract\ServiceProviderAbstracted;
    use STDW\View\Contract\ViewInterface;
    use STDW\View\Contract\ViewHandlerInterface;
    use STDW\View\View;


    class ServiceProvider extends ServiceProviderAbstracted
    {
        public function register(): void
        {
            $this->app->singleton(ViewInterface::class, View::class);
            $this->app->singleton(ViewHandlerInterface::class, ViewLatteHandler::class);
        }

        public function boot(): void
        { }

        public function terminate(): void
        { }
    }