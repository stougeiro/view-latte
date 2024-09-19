<?php declare(strict_types=1);

    namespace STDW\View\Latte;

    use STDW\View\Contract\ViewHandlerInterface;
    use Latte\Engine;


    class ViewLatteHandler implements ViewHandlerInterface
    {
        protected Engine $latte;


        public function __construct()
        {
            $this->latte = new Engine();
        }


        public function getEngine(): Engine
        {
            return $this->latte;
        }

        public function setTempDirectory(string $temporary_directory): ViewLatteHandler
        {
            if ( ! is_dir($temporary_directory) || ! file_exists($temporary_directory)) {
                throw ViewException::temporaryDirectoryNotFound($temporary_directory);
            }

            $this->latte->setTempDirectory($temporary_directory);

            return $this;
        }

        public function compile(string $filepath, array $data = []): string
        {
            if ( ! file_exists($filepath)) {
                throw ViewException::fileNotFound($filepath);
            }

            return $this->latte->renderToString($filepath, $data);
        }

        public function render(string $filepath, array $data = []): void
        {
            echo $this->compile($filepath, $data);
        }
    }