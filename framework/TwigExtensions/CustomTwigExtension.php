<?php

namespace Framework\TwigExtensions;

use \Twig\Extension\AbstractExtension;
use \Twig\TwigFilter;
use Twig\TwigFunction;

class CustomTwigExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('html_entity_decode', [$this, 'htmlEntityDecode'], ['is_safe' => ['html']]),
            new TwigFilter('sansedit', [$this, 'replaceContentEditable']),
            new TwigFilter('sanseditlp', [$this, 'removeContentEditableFromLandingPage']),
            new TwigFilter('sn', [$this, 'socialNetwork']),
            new TwigFilter('pad_left', [$this, 'padLeft']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getName', [$this, 'getName']),
        ];
    }    

    public function htmlEntityDecode($string)
    {
        return html_entity_decode($string);
    }

    public function replaceContentEditable($string)
    {
        $search_1 = 'contenteditable="true"';
        $search_2 = 'contenteditable';
        $replace = '';
        $string = str_replace($search_1, $replace, $string);
        $string = str_replace($search_2, $replace, $string);

        return $string;

    } 

    public function removeContentEditableFromLandingPage($string)
    {
        $string = preg_replace('/\s*contenteditable="[^"]*"/', '', $string);
        $string = preg_replace('/\s*tabindex="[^"]*"/', '', $string);
        $string = preg_replace('/\s*contenteditable\b/', '', $string);
        return $string;

    }     
    
    public function padLeft($input, $pad_string = '0', $pad_length = 2)
    {
        return str_pad($input, $pad_length, $pad_string, STR_PAD_LEFT);
    }    


    public function getName(string $email, ?string $fname = null, ?string $lname = null, ?string $name = null)
    {
        if (!empty($name)) {
            return $name;
        }

        if (!empty($fname) && !empty($lname)) {
            $lname = substr($lname, 0, 1);
            return trim("$fname $lname") . ".";
        }
    
        return !empty($fname) ? $fname : (function($email) {
            $split = explode("@", $email);
            $splitDomain = explode(".", $split[1]);
            $firstLetter = substr($split[0], 0, 1);
            $restMaskedLetters = str_repeat('*', strlen($split[0]) - 1);
            $maskedDomain = str_repeat('*', strlen($splitDomain[0]));
            
            $tld = $splitDomain[2] ?? null;
            return !empty($tld)
            ? $firstLetter."$restMaskedLetters@$maskedDomain." . $splitDomain[1] . ".$tld"
            : $firstLetter."$restMaskedLetters@$maskedDomain." . $splitDomain[1];
        })($email);
    }    
    
}