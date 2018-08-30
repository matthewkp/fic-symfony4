<?php

namespace App\Utils;

use Psr\Log\LoggerInterface;

class Slugger
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function run($text)
    {
        $this->logger->info('Slug input: ' . $text);

        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);

        $this->logger->info('Slug output: ' . $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
