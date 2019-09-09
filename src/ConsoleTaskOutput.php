<?php

declare(strict_types=1);

/**
 * This file is part of Laravel Console Task.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\LaravelConsoleTask;

use Illuminate\Console\OutputStyle;

/**
 * An output proxy for console tasks. Allows tasks to print additional
 * output during execution.
 * 
 * @package NunoMaduro\LaravelConsoleTask
 */
class ConsoleTaskOutput
{
    /** @var OutputStyle */
    protected $output;

    /** @var string */
    protected $indentation;

    /** @var int */
    protected $writes = 0;

    /**
     * ConsoleTaskOutput constructor.
     *
     * @param OutputStyle $output
     * @param string      $indentation
     */
    public function __construct(OutputStyle $output, string $indentation = '  -> ')
    {
        $this->output      = $output;
        $this->indentation = $indentation;
    }

    /**
     * Sets the indentation used for additional task output.
     * 
     * @param string $indentation
     * @return void
     */
    public function setIndentation(string $indendation): void
    {
        $this->indentation = $indendation;
    }

    /**
     * Writes a normal text line to the output.
     *
     * @param string $text
     * @return void
     */
    public function text(string $text): void
    {
        $this->writeln($text, '');
    }

    /**
     * Writes a success message to the output.
     *
     * @param string $text
     * @return void
     */
    public function success(string $text): void
    {
        $this->writeln($text, '<info>');
    }

    /**
     * Writes an error message to the output.
     *
     * @param string $text
     * @return void
     */
    public function error(string $text): void
    {
        $this->writeln($text, '<error>');
    }

    /**
     * Writes a caution warning to the output.
     *
     * @param string $text
     * @return void
     */
    public function caution(string $text): void
    {
        $this->writeln($text, '<fg=blue>');
    }

    /**
     * Writes a comment to the output.
     *
     * @param string $text
     * @return void
     */
    public function comment(string $text): void
    {
        $this->writeln($text, '<comment>');
    }

    /**
     * Writes a note to the output.
     *
     * @param string $text
     * @return void
     */
    public function note(string $text): void
    {
        $this->writeln($text, '<fg=magenta>');
    }

    /**
     * Returns whether this proxy has written to the output.
     *
     * @return bool
     */
    public function hasCreatedOutput(): bool
    {
        return $this->writes > 0;
    }

    /**
     * Returns the number of written lines.
     *
     * @return int
     */
    public function countWrittenLines(): int
    {
        return $this->writes;
    }

    /**
     * Writes a string to the output with a given format.
     *
     * @param string $text
     * @param string $format
     * @return void
     */
    protected function writeln(string $text, string $format): void
    {
        if ($this->writes === 0) {
            $this->output->newLine();
        }

        $this->output->writeln(sprintf('%s%s%s</>', $this->indentation, $format, $text));

        $this->writes++;
    }
}
