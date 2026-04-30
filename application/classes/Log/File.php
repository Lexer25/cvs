<?php
class Log_File extends Kohana_Log_File {

    const MAX_LOG_FILES = 30;
    
    public function __construct($directory)
    {
        // Нормализуем путь для Windows
        $directory = str_replace('/', DIRECTORY_SEPARATOR, $directory);
        
        // Проверяем существование папки
        if (!is_dir($directory)) {
            // Пробуем найти папку с разным регистром
            $parent = dirname($directory);
            $possibleDirs = array('logs', 'Logs', 'LOG', 'log');
            foreach ($possibleDirs as $dirName) {
                $testPath = $parent . DIRECTORY_SEPARATOR . $dirName;
                if (is_dir($testPath)) {
                    $directory = $testPath;
                    break;
                }
            }
        }
        
        parent::__construct($directory);
    }
    
    public function write(array $messages)
    {
        parent::write($messages);
        
        // Поиск всех PHP файлов (не только .log.php)
        $allLogFiles = $this->findAllLogFiles($this->_directory);
        $total = count($allLogFiles);
        
        if ($total <= self::MAX_LOG_FILES) {
            return;
        }
        
        // Сортируем по времени изменения
        usort($allLogFiles, array($this, 'compareFilemtime'));
        
        // Удаляем лишние
        $toDelete = $total - self::MAX_LOG_FILES;
        for ($i = 0; $i < $toDelete; $i++) {
            $file = $allLogFiles[$i];
            if (is_writable($file)) {
                @unlink($file);
            }
        }
        
        // Очищаем пустые папки
        $this->cleanEmptyFolders($this->_directory);
    }
    
    /**
     * Поиск всех PHP файлов в папке логов
     */
    protected function findAllLogFiles($dir)
    {
        $files = array();
        
        if (!is_dir($dir)) {
            return $files;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filename = $file->getFilename();
                // Проверяем: или .log.php, или просто .php (числовые имена)
                if (substr($filename, -8) === '.log.php' || 
                    (substr($filename, -4) === '.php' && is_numeric(substr($filename, 0, -4)))) {
                    $files[] = $file->getPathname();
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Очистка пустых папок
     */
    protected function cleanEmptyFolders($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $items = scandir($dir);
        $isEmpty = true;
        
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            
            if (is_dir($path)) {
                $this->cleanEmptyFolders($path);
                // Проверяем, пуста ли папка после очистки
                if ($this->isDirectoryEmpty($path)) {
                    @rmdir($path);
                } else {
                    $isEmpty = false;
                }
            } else {
                $isEmpty = false;
            }
        }
    }
    
    /**
     * Проверка, пуста ли директория
     */
    protected function isDirectoryEmpty($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }
        
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item != '.' && $item != '..') {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Сравнение времени изменения файлов
     */
    protected function compareFilemtime($a, $b)
    {
        $mtimeA = filemtime($a);
        $mtimeB = filemtime($b);
        
        if ($mtimeA == $mtimeB) {
            return 0;
        }
        
        return ($mtimeA < $mtimeB) ? -1 : 1;
    }
}