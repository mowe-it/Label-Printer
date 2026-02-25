<?php

namespace Talal\LabelPrinter\Command;

class ObjectCommand implements CommandInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function read()
    {
        if (empty($this->value)) {
            $this->value = '';
        }

        // Brother P-touch Template mode expects a single-byte code page (e.g. Windows-1252),
        // not UTF-8. Convert UTF-8 input to Windows-1252 so umlauts render correctly.
        if (function_exists('mb_check_encoding') && mb_check_encoding($this->value, 'UTF-8')) {
            if (function_exists('iconv')) {
                $converted = iconv('UTF-8', 'Windows-1252//TRANSLIT', $this->value);
                if ($converted !== false) {
                    $this->value = $converted;
                }
            } elseif (function_exists('mb_convert_encoding')) {
                $this->value = mb_convert_encoding($this->value, 'Windows-1252', 'UTF-8');
            }
        }

        $size = strlen($this->value);
        $n1 = intval($size % 256);
        $n2 = intval($size / 256);

        // Select the name
        $buffer = '^ON' . $this->name . chr(0);

        // Attach a value to the name
        $buffer .= '^DI' . chr($n1) . chr($n2) . $this->value;

        return $buffer;
    }
}
