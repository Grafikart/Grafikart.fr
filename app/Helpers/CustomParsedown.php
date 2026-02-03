<?php

namespace App\Helpers;

class CustomParsedown extends \Parsedown
{
    protected function blockFencedCode($Line)
    {
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
