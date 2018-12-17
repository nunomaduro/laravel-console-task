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

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;

/**
 * This is an Laravel Console Task Service Provider implementation.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class LaravelConsoleTaskServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        /*
         * Performs the given task, outputs and
         * returns the result.
         *
         * @param  string $title
         * @param  callable|null $task
         *
         * @return bool With the result of the task.
         */
        Command::macro(
            'task',
            function (string $title, $task = null, $loadingText = 'loading...') {
                $this->output->write("$title: <comment>{$loadingText}</comment>");

                if ($task === null) {
                    $result = true;
                } else {
                    try {
                        $result = $task() === false ? false : true;
                    } catch (\Exception $taskException) {
                        $result = false;
                    }
                }

                if ($this->output->isDecorated()) { // Determines if we can use escape sequences
                    // Move the cursor to the beginning of the line
                    $this->output->write("\x0D");

                    // Erase the line
                    $this->output->write("\x1B[2K");
                } else {
                    $this->output->writeln(''); // Make sure we first close the previous line
                }

                $this->output->writeln(
                    "$title: ".($result ? '<info>✔</info>' : '<error>failed</error>')
                );

                if (isset($taskException)) {
                    throw $taskException;
                }

                return $result;
            }
        );
    }
}
