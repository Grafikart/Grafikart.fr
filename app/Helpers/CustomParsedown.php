<?php

namespace App\Helpers;

/**
 * Handle custom syntax for code block and handle math expressions
 */
class CustomParsedown extends \Parsedown
{
    public function __construct()
    {
        $this->InlineTypes['$'][] = 'Math';
        $this->inlineMarkerList .= '$';
        $this->BlockTypes['$'][] = 'Math';
    }

    protected function blockMath($Line)
    {
        if (! str_starts_with($Line['text'], '$$')) {
            return;
        }

        // Match a complete display math block written on one line: $$ formula $$.
        if (preg_match('/^\$\$[ ]*(.+?)[ ]*\$\$[ ]*$/', $Line['text'], $matches)) {
            return [
                'element' => $this->mathElement($matches[1], true),
                'complete' => true,
            ];
        }

        // Match the opening line of a multi-line display math block: $$ followed by optional first-line content.
        if (preg_match('/^\$\$[ ]*(.*)$/', $Line['text'], $matches)) {
            return [
                'element' => $this->mathElement($matches[1], true),
            ];
        }
    }

    protected function blockMathContinue($Line, $Block)
    {
        if (isset($Block['complete'])) {
            return;
        }

        if (isset($Block['interrupted'])) {
            $Block['element']['text'] .= "\n";

            unset($Block['interrupted']);
        }

        // Match the closing line of a multi-line display math block and keep any formula text before the final $$.
        if (preg_match('/^(.*?)[ ]*\$\$[ ]*$/', $Line['text'], $matches)) {
            if ($matches[1] !== '') {
                $Block['element']['text'] .= "\n".$matches[1];
            }

            $Block['complete'] = true;

            return $Block;
        }

        $Block['element']['text'] .= "\n".$Line['body'];

        return $Block;
    }

    protected function blockMathComplete($Block)
    {
        if (isset($Block['complete'])) {
            return $Block;
        }

        $Block['element'] = [
            'name' => 'p',
            'text' => '$$'.$Block['element']['text'],
            'handler' => 'line',
        ];

        return $Block;
    }

    protected function inlineMath($Excerpt)
    {
        // Match inline display math delimited by $$...$$, excluding empty content, trailing whitespace, and escaped closers.
        if (preg_match('/^\$\$(?=\S)(.+?)(?<!\s)(?<!\\\\)\$\$/s', $Excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => $this->mathElement($matches[1], true),
            ];
        }

        // Match inline math delimited by single dollars, while avoiding $$ blocks, empty content, trailing whitespace, and escaped closers.
        if (preg_match('/^\$(?!\$)(?=\S)(.+?)(?<!\s)(?<!\\\\)\$(?!\$)/s', $Excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => $this->mathElement($matches[1], false),
            ];
        }
    }

    private function mathElement(string $text, bool $display): array
    {
        return [
            'name' => 'math-formula',
            'text' => trim($text),
            'attributes' => [
                'display' => $display ? 'block' : 'inline',
            ],
        ];
    }

    protected function blockFencedCode($Line)
    {
        // Match an opening fenced code block made of 3+ repeated fence chars, with an optional language hint.
        if (preg_match('/^['.$Line['text'][0].']{3,}[ ]*([^`]+)?[ ]*$/', $Line['text'], $matches)) {
            $element = [
                'name' => 'code-block',
                'text' => '',
            ];

            if (isset($matches[1])) {
                $language = substr($matches[1], 0, strcspn($matches[1], " \t\n\f\r"));
                $element['attributes'] = [
                    'lang' => $language,
                ];
            }

            $block = [
                'char' => $Line['text'][0],
                'element' => $element,
            ];

            return $block;
        }
    }

    protected function blockFencedCodeContinue($Line, $Block)
    {
        if (isset($Block['complete'])) {
            return;
        }

        if (isset($Block['interrupted'])) {
            $Block['element']['text'] .= "\n";

            unset($Block['interrupted']);
        }

        // Match the closing fence using the same fence char and allowing only trailing spaces.
        if (preg_match('/^'.$Block['char'].'{3,}[ ]*$/', $Line['text'])) {
            $Block['element']['text'] = substr($Block['element']['text'], 1);

            $Block['complete'] = true;

            return $Block;
        }

        $Block['element']['text'] .= "\n".$Line['body'];

        return $Block;
    }

    protected function blockFencedCodeComplete($Block)
    {
        $text = $Block['element']['text'];

        $Block['element']['text'] = $text;

        return $Block;
    }
}
