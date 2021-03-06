<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Strings\Multibyte;

class StringUtilities {

    /**
     * Pads a multibyte string to a certain length with another string
     *
     * @param string $input the input string
     * @param int $padLength the target string size
     * @param string $padString the padding characters (optional, default is space)
     * @param int $padType STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH (optional, default is STR_PAD_RIGHT)
     * @param string $encoding the character encoding (optional)
     *
     * @return string the padded string
     *
     */
    public static function pad (
        string $input,
        int $padLength,
        string $padString = ' ',
        int $padType = STR_PAD_RIGHT,
        string $encoding = ''
    ) : string {
        return (new StringPad)
            ->setInput($input)
            ->setPadLength($padLength)
            ->setPadString($padString)
            ->setPadType($padType)
            ->setEncoding($encoding ?: mb_internal_encoding())
            ->pad();
    }

    public static function isSupportedEncoding (string $encoding) : bool {
        foreach (mb_list_encodings() as $supportedEncoding) {
            if ($encoding === $supportedEncoding) {
                return true;
            }
        }

        return false;
    }

    public static function startsWith (string $string, string $start) {
        $length = mb_strlen($start);
        return mb_substr($string, 0, $length) === $start;
    }

    public static function endsWith (string $string, string $end) {
        $length = mb_strlen($end);
        return $length === 0 || mb_substr($string, -$length) === $end;
    }

    public static function contains (string $string, string $needle) : bool {
        return strpos($string, $needle) !== false;
    }

}
