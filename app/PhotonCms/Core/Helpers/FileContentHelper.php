<?php

namespace Photon\PhotonCms\Core\Helpers;

class FileContentHelper
{
    /**
     * New line character for class files.
     * Double quotes are mandatory!
     *
     * @var string
     */
    protected static $newLineCharacter = "\n";

    /**
     * Desired indent for the code.
     * For tabulator use \t
     * Double quotes are mandatory!
     *
     * @var string
     */
    protected static $indentCharacter = "    ";

    /**
     * Adds new lines to the passed content variable reference.
     *
     * @param string $content
     * @param int $numberOfLines
     */
    public static function addNewLines(&$content, $numberOfLines = 1)
    {
        while ($numberOfLines > 0) {
            $content .= self::$newLineCharacter;
            $numberOfLines--;
        }
    }

    /**
     * Adds indentation to the passed content reference.
     *
     * @param string $content
     * @param int $numberOfIndents
     */
    public static function addIndent(&$content, $amountOfIndent = 1)
    {
        while ($amountOfIndent > 0) {
            $content .= self::$indentCharacter;
            $amountOfIndent--;
        }
    }

    /**
     * Adds lines and indents the referenced content for a specified amount of times.
     *
     * @param reference $content
     * @param int $numberOfLines
     * @param int $amountOfIndent
     */
    public static function addLinesAndIndent(&$content, $numberOfLines = 1, $amountOfIndent = 1)
    {
        self::addNewLines($content, $numberOfLines);
        self::addIndent($content, $amountOfIndent);
    }

    public static function getNewLines($numberOfLines = 1)
    {
        $content = '';
        while ($numberOfLines > 0) {
            $content .= self::$newLineCharacter;
            $numberOfLines--;
        }

        return $content;
    }

    public static function getIndent($amountOfIndent = 1)
    {
        $content = '';
        while ($amountOfIndent > 0) {
            $content .= self::$indentCharacter;
            $amountOfIndent--;
        }

        return $content;
    }

    public static function getLinesAndIndent($numberOfLines = 1, $amountOfIndent = 1)
    {
        $content = '';
        while ($numberOfLines > 0) {
            $content .= self::$newLineCharacter;
            $numberOfLines--;
        }
        
        while ($amountOfIndent > 0) {
            $content .= self::$indentCharacter;
            $amountOfIndent--;
        }

        return $content;
    }
}