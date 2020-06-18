<?php

class HtmlParser
{
    const TOKEN_TAG_START  = 'tag_start';
    const TOKEN_TAG_END    = 'tag_end';
    const TOKEN_TAG_SINGLE = 'tag_single';
    const TOKEN_TEXT       = 'text';

    const NODE_TAG         = 'tag';
    const NODE_TEXT        = 'text';

    /**
     * Return token from the beginning of the string
     *
     * @param string $string
     * @return array
     */
    public function getToken(string $string) : array
    {
        //Self-closing tag
        if (preg_match('#^<([a-zA-Z0-9]+) */>#', $string, $matches)) {
            return [
                'type' => self::TOKEN_TAG_SINGLE,
                'content' => $matches[0],
                'tag' => $matches[1]
            ];
        }

        //Closing tag
        if (preg_match('#^</([a-zA-Z0-9]+) *>#', $string, $matches)) {
            return [
                'type' => self::TOKEN_TAG_END,
                'content' => $matches[0],
                'tag' => $matches[1]
            ];
        }

        //Opening tag
        if (preg_match('#^<([a-zA-Z0-9]+) *>#', $string, $matches)) {
            return [
                'type' => self::TOKEN_TAG_START,
                'content' => $matches[0],
                'tag' => $matches[1],
            ];
        }

        //String before tag
        if (preg_match('#^(.+?)</?([a-zA-Z0-9]+) *>#', $string, $matches)) {
            return [
                'type' => self::TOKEN_TEXT,
                'content' => $matches[1]
            ];
        }

        //String
        return [
            'type' => self::TOKEN_TEXT,
            'content' => $string
        ];
    }

    /**
     * Translates html to array of elements
     *
     * @param string $string
     * @return array
     * @throws Exception
     */
    public function parse(string $string) : array
    {
        $structure = [];
        $currentPointer = &$structure;
        $pointers = [&$currentPointer];

        while (strlen($string)) {
            $token = $this->getToken($string);

            if ($token['type'] == self::TOKEN_TAG_SINGLE) {
                $currentPointer []= [
                    'type' => self::NODE_TAG,
                    'name' => $token['tag'],
                    'children' => [],
                ];
            } else if ($token['type'] == self::TOKEN_TEXT) {
                $currentPointer []= [
                    'type' => self::NODE_TEXT,
                    'content' => $token['content'],
                ];
            } else if ($token['type'] == self::TOKEN_TAG_START) {
                $currentPointer []= [
                    'type' => self::NODE_TAG,
                    'name' => $token['tag'],
                    'children' => [],
                ];
                $currentPointer = &$currentPointer[count($currentPointer) - 1]['children'];
                $pointers []= &$currentPointer;
            } else if ($token['type'] == self::TOKEN_TAG_END) {
                if (count($pointers) < 2) {
                    throw new Exception('Found closing tag without corresponding opening tag');
                }
                array_pop($pointers);
                $currentPointer = &$pointers[count($pointers) - 1];
                if ($currentPointer[count($currentPointer) - 1]['name'] != $token['tag']) {
                    throw new Exception('Closing tag does not match opening tag');
                }
            }

            $string = substr($string, strlen($token['content']));
        }

        if (count($pointers) > 1) {
            throw new Exception('Found opening tag without closing tag');
        }

        return $structure;
    }
}
