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

namespace NunoMaduro\Tests\LaravelConsoleTask;

use ReflectionClass;
use Illuminate\Console\Command;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use NunoMaduro\LaravelConsoleTask\LaravelConsoleTaskServiceProvider;

/**
 * This is the service provider test class.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class ServiceProviderTest extends TestCase
{
    public function testSuccessfulTask()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputInterface::class);

        $outputMock->expects($this->once())
            ->method('writeln')
            ->with('Foo: <info>✔</info>');

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertTrue(
            $command->task(
                'Foo',
                function () {
                    return true;
                }
            )
        );
    }

    public function testUnsuccessfulTask()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputInterface::class);

        $outputMock->expects($this->once())
            ->method('writeln')
            ->with('Bar: <error>failed</error>');

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertFalse(
            $command->task(
                'Bar',
                function () {
                    return false;
                }
            )
        );
    }
}
