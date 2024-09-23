<?php declare(strict_types=1);

    namespace STDW\View\Latte;

    use STDW\View\Contract\ViewHandlerInterface;
    use STDW\View\Latte\ValueObject\StorageValue;
    use STDW\Support\Str;
    use Latte\Engine;


    class ViewLatteHandler implements ViewHandlerInterface
    {
        protected array $storage = [];

        protected string $storage_separator = ':';

        protected string $file_extension = '.html';

        protected array $data = [];

        protected Engine $latte;


        public function __construct()
        {
            $this->latte = new Engine();
        }


        public function getEngine(): Engine
        {
            return $this->latte;
        }

        public function setTempDirectory(string $temporary_directory): void
        {
            $temp = StorageValue::create($temporary_directory);

            if ( ! $temp->isValid()) {
                throw new ViewException("View: '{$temp->get()}' not found or not is a valid directory");
            }

            $this->latte->setTempDirectory($temp->get());
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

            $this->compose($data);

            return $this->latte->renderToString($filepath, $this->data);
        }

        public function render(string $filepath, array $data = []): void
        {
            echo $this->compile($filepath, $data);
        }
    }