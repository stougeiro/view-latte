<?php declare(strict_types=1);

    namespace STDW\View\Latte;

    use STDW\View\Contract\ViewHandlerInterface;
    use STDW\View\Latte\ValueObject\StorageValue;
    use STDW\Support\Str;
    use Latte\Engine;
    use Throwable;


    class ViewLatteHandler implements ViewHandlerInterface
    {
        protected string $storage_separator = ':';

        protected string $file_extension = '.html';

        protected string $environment = '';

        protected array $storage = [];

        protected array $data = [];

        protected Engine $latte;


        public function __construct()
        {
            $this->latte = new Engine();

            $this->latte->addFunction('view', function(string $filepath)
            {
                return $this->getTemplatePath($filepath);
            });


            try {
                $this->storage_separator = config('view.storage_separator');
            } catch (Throwable $e) { }

            try {
                $this->file_extension = config('view.file_extension');
            } catch (Throwable $e) { }


            try {
                $temporary_directory = config('view.temporary_directory');
            } catch (Throwable $e) {
                $temporary_directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
            }

            $this->setTempDirectory($temporary_directory);


            try {
                $this->environment = env('environment');
            } catch (Throwable $e) { }

            if ($this->environment == 'production') {
                $this->latte->setAutoRefresh(false);
                $this->latte->setStrictParsing();
                $this->latte->setStrictTypes();
            }
        }


        public function getEngine(): Engine
        {
            return $this->latte;
        }

        public function setStorage(string $name, string $path): void
        {
            $path = StorageValue::create($path);

            if ( ! $path->isValid()) {
                throw new ViewException("View: '{$path->get()}' not found or not is a valid directory");
            }

            if (Str::empty($name) || in_array($name, array_keys($this->storage))) {
                throw new ViewException("View: '{$name}' is empty or already exists in storage collection");
            }

            $this->storage[$name] = $path->get();
        }

        public function compose(array $data): void
        {
            $this->data = array_merge($this->data, $data);
        }

        public function compile(string $filepath, array $data = []): string
        {
            $this->compose($data);

            return $this->latte->renderToString( $this->getTemplatePath($filepath), $this->data);
        }

        public function render(string $filepath, array $data = []): void
        {
            echo $this->compile($filepath, $data);
        }


        protected function setTempDirectory(string $temporary_directory): void
        {
            $temp = StorageValue::create($temporary_directory);

            if ( ! $temp->isValid()) {
                throw new ViewException("View: '{$temp->get()}' not found or not is a valid directory");
            }

            $this->latte->setTempDirectory($temp->get());
        }

        protected function getTemplatePath(string $filepath): string
        {
            if (strpos($filepath, $this->storage_separator)) {
                list($storage, $filepath) = explode($this->storage_separator, $filepath);

                $filepath = ($this->storage[$storage] ?? null) . $filepath;
            }

            if (strpos($filepath, '.')) {
                $filepath = str_replace('.', DIRECTORY_SEPARATOR, $filepath);
            }

            if ( ! strpos($filepath, $this->file_extension)) {
                $filepath .= $this->file_extension;
            }

            if ( ! file_exists($filepath)) {
                throw new ViewException("View: '{$filepath}' not found");
            }

            return $filepath;
        }
    }