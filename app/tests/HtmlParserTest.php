<?php declare(strict_types=1);

require_once __DIR__ . '/../src/HtmlParser.php';

use PHPUnit\Framework\TestCase;

class HtmlParserTest extends TestCase
{
    /**
     * @dataProvider getElementFromStartProvider
     */
    public function testGetElementFromStart($string, $expected): void
    {
        $parser = new HtmlParser();
        $this->assertEquals(
            $expected,
            $parser->getToken($string)
        );
    }

    public function getElementFromStartProvider() : array
    {
        return [
            [
                '<b>123',
                [
                    'type' => HtmlParser::TOKEN_TAG_START,
                    'content' => '<b>',
                    'tag' => 'b',
                ]
            ],
            [
                '< b>123',
                [
                    'type' => HtmlParser::TOKEN_TEXT,
                    'content' => '< b>123',
                ]
            ],
            [
                '< <<>a>d/>s/<< ',
                [
                    'type' => HtmlParser::TOKEN_TEXT,
                    'content' => '< <<>a>d/>s/<< ',
                ]
            ],
            [
                '<b/>123',
                [
                    'type' => HtmlParser::TOKEN_TAG_SINGLE,
                    'content' => '<b/>',
                    'tag' => 'b',
                ]
            ],
            [
                '<b />123',
                [
                    'type' => HtmlParser::TOKEN_TAG_SINGLE,
                    'content' => '<b />',
                    'tag' => 'b',
                ]
            ],
            [
                'some_text',
                [
                    'type' => HtmlParser::TOKEN_TEXT,
                    'content' => 'some_text',
                ]
            ],
            [
                'some_text<div>',
                [
                    'type' => HtmlParser::TOKEN_TEXT,
                    'content' => 'some_text',
                ]
            ],
            [
                'some_text<>',
                [
                    'type' => HtmlParser::TOKEN_TEXT,
                    'content' => 'some_text<>',
                ]
            ],
        ];
    }

    /**
     * @dataProvider parseProvider
     * @throws Exception
     */
    public function testParse($string, $expected): void
    {
        $parser = new HtmlParser();
        $this->assertEquals(
            $expected,
            $parser->parse($string)
        );
    }

    public function parseProvider() : array
    {
        return [
            [
                '123',
                [
                    [
                        'type' => HtmlParser::TOKEN_TEXT,
                        'content' => '123',
                    ]
                ]
            ],
            [
                '123<h1 234',
                [
                    [
                        'type' => HtmlParser::TOKEN_TEXT,
                        'content' => '123<h1 234',
                    ]
                ]
            ],
            [
                '<div>123</div>',
                [
                    [
                        'type' => HtmlParser::NODE_TAG,
                        'name' => 'div',
                        'children' => [
                            [
                                'type' => HtmlParser::NODE_TEXT,
                                'content' => '123',
                            ]
                        ]
                    ]

                ]
            ],
            [
                '<div>123</div>345',
                [
                    [
                        'type' => HtmlParser::NODE_TAG,
                        'name' => 'div',
                        'children' => [
                            [
                                'type' => HtmlParser::NODE_TEXT,
                                'content' => '123',
                            ]
                        ]
                    ],
                    [
                        'type' => HtmlParser::NODE_TEXT,
                        'content' => '345',
                    ]
                ]
            ],
            [
                'str1<div>str2<b>str3</b>str4<p>str5</p></div>str6',
                [
                    [
                        'type' => HtmlParser::NODE_TEXT,
                        'content' => 'str1',
                    ],
                    [
                        'type' => HtmlParser::NODE_TAG,
                        'name' => 'div',
                        'children' => [
                            [
                                'type' => HtmlParser::NODE_TEXT,
                                'content' => 'str2',
                            ],
                            [
                                'type' => HtmlParser::NODE_TAG,
                                'name' => 'b',
                                'children' => [
                                    [
                                        'type' => HtmlParser::NODE_TEXT,
                                        'content' => 'str3',
                                    ]
                                ]
                            ],
                            [
                                'type' => HtmlParser::NODE_TEXT,
                                'content' => 'str4',
                            ],
                            [
                                'type' => HtmlParser::NODE_TAG,
                                'name' => 'p',
                                'children' => [
                                    [
                                        'type' => HtmlParser::NODE_TEXT,
                                        'content' => 'str5',
                                    ]
                                ]
                            ],
                        ]
                    ],
                    [
                        'type' => HtmlParser::NODE_TEXT,
                        'content' => 'str6',
                    ]
                ]
            ],
            [
                '<br />',
                [
                    [
                        'type' => HtmlParser::NODE_TAG,
                        'name' => 'br',
                        'children' => [],
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider parseInvalidProvider
     * @expectedException \Exception
     */
    public function testParseInvalid($string, $exceptionMessage): void
    {
        $parser = new HtmlParser();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage($exceptionMessage);
        $parser->parse($string);
    }

    public function parseInvalidProvider() : array
    {
        return [
            ['<b>123</textarea>', 'Closing tag does not match opening tag'],
            ['<b><div>123</div></b></div>', 'Found closing tag without corresponding opening tag'],
            ['</b>', 'Found closing tag without corresponding opening tag'],
            ['<b>', 'Found opening tag without closing tag'],
            ['<br /><b><br/>', 'Found opening tag without closing tag'],
        ];
    }

}
