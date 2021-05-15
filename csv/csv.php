<?php

namespace regasen\libraries\csv;

class csv
{
    /**
     * Соль для генерации названия файла по умолчанию
     *
     * @var string
     */
    private $_salt = 'f108f01upg1fisabof108fqb';

    /**
     * Название CSV файла
     *
     * @var string
     */
    private $_name = '';

    /**
     * Режим отладки
     * Позволяет вывести или получить массив параметров о времени работы скрипта
     * И используемой памяти
     *
     * @var boolean
     */
    private $_debug = true;

    /**
     * Массив отладочных параметров
     *
     * @var array
     */
    private $_debug_data = [];

    /**
     * Путь до директории, куда поместить CSV файл
     * В конце пути необходимо указать /
     *
     * @var string
     */
    private $_path = __PATH__ . '/files/';

    /**
     * Заголовки таблицы
     * Необязательный параметр
     * Если длина массива заголовков не будет совпадать с длиной массива строки
     * В параметры отладки будет помещена соответствующая информация
     *
     * @var array
     */
    private $_titles = [];

    /**
     * Массив параметров декодирования строки
     * Элемент in - исходная кодировка
     * Элемент out - итоговая кодировка
     *
     * @var array
     */
    private $_decode_params = ['in' => 'UTF-8', 'out' => 'cp-1251'];

    public function __construct()
    {
        if ($this->_debug) {
            $this->_debug_data['start_time'] = microtime(true);
            $this->_debug_data['start_memory'] = memory_get_usage();
        }
    }

    /**
     * Получить сгенерированный CSV
     *
     * @param array $input
     * @return string
     */
    public function getCSV(array $input): string
    {
        $this->generate($input)->getDebug(true);
        if (!file_exists($this->_path)) mkdir($this->_path);
        if (!file_exists($this->_path . date('Ymd'))) mkdir($this->_path . date('Ymd'));
        return $this->_path . date('Ymd') . '/' . $this->_name;
    }

    /**
     * Установить заголовки
     *
     * @param array $titles
     * @return void
     */
    public function setTitles(array $titles = [])
    {
        $this->_titles = $titles;
        return $this;
    }

    /**
     * Получить массив с отладочной информацией
     * Если параметр $return_json=TRUE, то метод вернет JSON-массив
     *
     * @param boolean $return_json
     * @return void
     */
    public function getDebugData($return_json = false)
    {
        if ($return_json) {
            return json_encode($this->_debug_data);
        }
        return $this->_debug_data;
    }

    /**
     * Установить имя CSV файла
     *
     * @param string $name
     * @return this
     */
    public function setName(string $name = '')
    {
        if ($name == '') {
            $name = md5(time() . $this->_salt) . '-' . date('d-m-y-H-i-s');
        }
        $this->_name = $name;
        return $this;
    }

    /**
     * Установить путь до CSV файла
     *
     * @param string $path
     * @return this
     */
    public function setPath(string $path = '')
    {
        $this->_path = $path;
        return $this;
    }

    /**
     * Сгенерировать CSV файл
     *
     * @param array $input
     * @return this
     */
    public function generate(array $input)
    {
        $output = '';

        if (!empty($this->_titles)) {
            $output .= '"' . $this->decode(implode('";"', $this->_titles)) . '"' . "\n";
            if (isset($input[0]) && count($input[0]) !== count($this->_titles)) {
                $this->_debug_data['titles'] = 'Titles count no match the input counts';
            }
        }

        foreach ($input as $value) {
            $output .= '"' . $this->decode(implode('";"', $value)) . '"' . "\n";
        }
        if (!file_exists($this->_path)) mkdir($this->_path);
        if (!file_exists($this->_path . date('Ymd'))) mkdir($this->_path . date('Ymd'));
        file_put_contents($this->_path . date('Ymd') . '/' . $this->_name . '.csv', $output);
        return $this;
    }

    /**
     * Декодировать строку
     *
     * @param string $text
     * @return string
     */
    public function decode(string $text = ''): string
    {
        return mb_convert_encoding($text, $this->_decode_params['out'], $this->_decode_params['in']);
    }

    /**
     * Получить данные по времени выполнения скрипта и по используемой памяти
     * Если $return_json указан TRUE, то метод вернет JSON-массив с данными
     * Иначе, метод выведет данные на экран (echo)
     *
     * @param boolean $return_json
     * @return void
     */
    public function getDebug($return_json = false)
    {
        if ($this->_debug) {
            $this->_debug_data['end_time'] = round(microtime(true) - $this->_debug_data['start_time'], 2);
            $this->_debug_data['end_memory'] = abs(memory_get_usage() - $this->_debug_data['start_memory']);
            if ($return_json) {
                return json_encode($this->_debug_data);
            }
            echo $this->_debug_data['end_time'] . ' сек.' . "\n\r<br>" . $this->_debug_data['end_memory'] . ' Байт';
        }
    }
}
