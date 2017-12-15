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
         * @param  callable $task
         *
         * @return bool With the result of the task.
         */
        Command::macro(
            'task',
            function (string $title, callable $task) {
                return tap($task(), function ($result) use ($title) {
                    $this->output->writeln(
                        "$title: ".($result ? '<info>✔</info>' : '<error>failed</error>')
                    );
                });
            }
        );
    }
}
